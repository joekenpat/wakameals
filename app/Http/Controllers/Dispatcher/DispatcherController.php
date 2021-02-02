<?php

namespace App\Http\Controllers\Dispatcher;

use App\Http\Controllers\Controller;
use App\Models\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class DispatcherController extends Controller
{
  /**
   * Display a resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function show()
  {
    $dispatcher = Dispatcher::whereId(Auth('dispatcher')->user()->id)->with(['place'])->firstOrFail();
    $response['status'] = 'success';
    $response['details'] = $dispatcher;
    return response()->json($response, Response::HTTP_OK);
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function default_register(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|string|between:3,240',
      'phone' => 'sometimes|nullable|string|max:15|min:8|unique:dispatchers,phone',
      'place' => 'required|integer|exists:places,id',
      'address' => 'required|string',
      'type' => 'required|in:door_delivery,pickup',
      'email' => 'required|email|unique:dispatchers,email',
      'password' => 'required|string|',
    ]);

    $attribs = [
      'name',
      'phone',
      'place',
      'address',
      'type',
      'email',
      'password'
    ];

    $new_dispatcher = new Dispatcher();
    foreach ($attribs as $attrib) {
      if ($attrib == 'password') {
        $new_dispatcher->{$attrib} = Hash::make($request->{$attrib});
      } else if ($attrib == 'place') {
        $new_dispatcher->{$attrib . '_id'} = $request->{$attrib};
      } else {
        $new_dispatcher->{$attrib} = $request->{$attrib};
      }
    }

    $new_dispatcher->status = 'pending';
    $new_dispatcher->last_login = now()->format('Y-m-d H:i:s.u');
    $new_dispatcher->last_ip = request()->getClientIp();
    $new_dispatcher->save();
    $new_dispatcher->save();
    $response['status'] = 'success';
    $response['message'] = 'Account has been created';
    $response['token'] = $new_dispatcher->createToken(config('app.name') . '_personal_access_token', ['dispatcher'])->accessToken;
    return response()->json($response, Response::HTTP_CREATED);
  }

  /**
   * Dispatcher Default login
   *
   * @param \illuminate\Http\Client\Request $request
   * @return \Illuminate\Http\
   */
  public function default_login(Request $request)
  {
    $auth_by = $this->find_default_auth_by($request);

    $messages = [
      'identifier.required' => 'Email or Phone Number is Required',
      'email.exists' => 'No Account With That Email',
      'phone.exists' => 'No Account With That Phone Number',
      'password.required' => 'Password cannot be empty',
    ];

    $this->validate($request, [
      'identifier' => 'required|string',
      'password' => 'required|string',
      'email' => 'sometimes|string|exists:dispatchers,' . $auth_by,
    ], $messages);

    if (Dispatcher::where("$auth_by", $request->input('identifier'))->exists()) {
      $dispatcher = Dispatcher::where($auth_by, $request->input('identifier'))->first();
      if (password_verify($request->input('password'), $dispatcher->password)) {
        $this->auth_success($dispatcher);
        $response['status'] = 'success';
        $response['message'] = 'Log-in Successfull';
        $response['token'] = $dispatcher->createToken(config('app.name') . '_personal_access_token', ['dispatcher'])->accessToken;
        return response()->json($response, Response::HTTP_OK);
      } else {
        $response['message'] = 'Invalid Credentials';
        $response['errors'] = ['password' => ['Password Incorrect']];
        return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    } else {
      $response['message'] = 'Invalid Credentials';
      $response['errors'] = ["$auth_by" => ['No account with that ' . "$auth_by"]];
      return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }


  /**
   * Update Dispatcher.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    $this->validate($request, [
      'name' => 'sometimes|nullable||between:3,240',
      'phone' => 'sometimes|nullable|string|max:15|min:8|unique:users,phone,' . auth('dispatcher')->user()->id,
      'place' => 'sometimes|nullable|alpha_dash|exists:places,id',
      'address' => 'sometimes|nullable|string|min:5|max:255',
      'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);
    try {
      $dispatcher = Dispatcher::where('id', Auth('dispatcher')->user()->id)->firstOrFail();
      //adding images
      if ($request->hasFile('avatar') && $request->avatar != null) {
        $image = Image::make($request->file('avatar'))->encode('jpg', 1);
        if (Auth('dispatcher')->user()->avatar != null) {
          if (File::exists("images/dispatchers/" . Auth('dispatcher')->user()->avatar)) {
            File::delete("images/dispatchers/" . Auth('dispatcher')->user()->avatar);
          }
        }
        if (!File::isDirectory(public_path("images/dispatchers/"))) {
          File::makeDirectory(public_path("images/dispatchers"));
        }
        $img_name = sprintf("DISPATCHER%s.jpg", strtolower(Str::random(15)));
        $image->save(public_path("images/dispatchers/") . $img_name, 70, 'jpg');
        $request->avatar = $img_name;
        $dispatcher->avatar = $img_name;
      }
      $attribs = [
        'name',
        'phone',
        'place',
        'address',
        'password'
      ];
      foreach ($attribs as $attrib) {
        if ($request->has($attrib) && $request->{$attrib} != (null || '')) {
          if ($attrib == 'dob') {
            $dispatcher->{$attrib} = Carbon::parse($request->{$attrib});
          } elseif ($attrib == 'place') {
            $dispatcher->{$attrib . '_id'} = $request->{$attrib};
          } else {
            $dispatcher->{$attrib} = $request->{$attrib};
          }
        }
      }
      $dispatcher->update();

      $response['status'] = 'success';
      $response['message'] = 'Profile has been updated';
      $response['dispatcher'] = $dispatcher;
      return response()->json($response, Response::HTTP_OK);
    } catch (ModelNotFoundException $mnt) {
      $response['status'] = 'error';
      $response['message'] = 'Dispatcher not Found';
      return response()->json($response, Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function logout()
  {
    Auth::guard('dispatcher')->token()->revoke();
    Auth::guard('dispatcher')->logout();
    $response['status'] = 'success';
    $response['message'] = 'Dispatcher Logged Out';
    return response()->json($response, Response::HTTP_OK);
  }

  public function list_dispatcher_notifications()
  {
    $dispatcher = Dispatcher::find(Auth('dispatcher')->user()->id)->first();
    $notifications = $dispatcher->unreadNotifications()->paginate(20);
    $response['status'] = 'success';
    $response['notifications'] = $notifications;
    return response()->json($response, Response::HTTP_OK);
  }

  public function mark_dispatcher_notification_as_read($notification_id)
  {
    $dispatcher = Dispatcher::find(Auth('dispatcher')->user()->id)->first();
    $notification = $dispatcher->notifications()->whereId($notification_id)->first();
    $notification->markAsRead();
    $response['status'] = 'success';
    $response['messages'] = 'Notification marked as Read';
    return response()->json($response, Response::HTTP_OK);
  }


  public function mark_dispatcher_all_notification_as_read()
  {
    $dispatcher = Dispatcher::find(Auth('dispatcher')->user()->id)->first();
    $dispatcher->unreadNotifications()->update(['read_at' => now()]);
    $response['status'] = 'success';
    $response['messages'] = 'All notifications marked as Read';
    return response()->json($response, Response::HTTP_OK);
  }

  public function find_default_auth_by(Request $request)
  {
    $login_data = $request->identifier;
    if (filter_var($login_data, FILTER_VALIDATE_EMAIL)) {
      $login_field_type = 'email';
    } else {
      $login_field_type = 'phone';
    }
    request()->merge([$login_field_type => $login_data]);
    return $login_field_type;
  }

  public function update_password(Request $request)
  {
    $this->validate($request, [
      'current_password' => 'required|string',
      'new_password' => 'required|string',
      'retype_new_password' => 'required|string|same:new_password',
    ]);

    $dispatcher = Dispatcher::whereId(auth('dispatcher')->user()->id)->firstOrFail();
    if (password_verify($request->input('current_password'), $dispatcher->password)) {
      $dispatcher->password = Hash::make($request->input('new_password'));
      $dispatcher->update;
      $this->auth_success($dispatcher);
      $response['status'] = 'success';
      $response['message'] = 'Password Change Successfull';
      $response['token'] = $dispatcher->createToken(config('app.name') . '_personal_access_token', ['dispatcher'])->accessToken;
      return response()->json($response, Response::HTTP_OK);
    } else {
      $response['message'] = 'Invalid Credentials';
      $response['errors'] = ['current_password' => ['Current Password is not correct']];
      return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }

  protected function auth_success($dispatcher)
  {
    $dispatcher->update([
      'last_login' => now()->format('Y-m-d H:i:s.u'),
      'last_ip' => request()->getClientIp(),
    ]);
    return;
  }
}
