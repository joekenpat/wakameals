<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Chef;
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

class ChefController extends Controller
{
  /**
   * Display a resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function show()
  {
    $chef = Chef::whereId(Auth('chef')->user()->id)->with(['place', 'dispatcher'])->firstOrFail();
    $response['status'] = 'success';
    $response['details'] = $chef;
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
      'dispatcher_code' => 'required|alpha_num|size:6|exists:dispatchers,code',
      'email' => 'required|email|unique:chefs,email',
      'password' => 'required|string|',
    ]);

    $attribs = [
      'name',
      'phone',
      'place',
      'email',
      'password'
    ];

    $dispatcher = Dispatcher::whereCode($request->dispatcher_code)->whereType('pickup')->firstOrFail();
    $new_chef = new Chef();
    $new_chef->dispatcher_id = $dispatcher->id;
    foreach ($attribs as $attrib) {
      if ($attrib == 'place') {
        $new_chef->{$attrib . '_id'} = $request->{$attrib};
      } else {
        $new_chef->{$attrib} = $request->{$attrib};
      }
    }

    $new_chef->status = 'pending';
    $new_chef->last_login = now()->format('Y-m-d H:i:s.u');
    $new_chef->last_ip = request()->getClientIp();
    $new_chef->save();
    $new_chef->save();
    $response['status'] = 'success';
    $response['message'] = 'Account has been created';
    $response['token'] = $new_chef->createToken(config('app.name') . '_personal_access_token', ['chef'])->accessToken;
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
      'email' => 'sometimes|string|exists:chefs,' . $auth_by,
    ], $messages);

    if (Chef::where("$auth_by", $request->input('identifier'))->exists()) {
      $chef = Chef::where($auth_by, $request->input('identifier'))->first();
      if (password_verify($request->input('password'), $chef->password)) {
        $this->auth_success($chef);
        $response['status'] = 'success';
        $response['message'] = 'Log-in Successfull';
        $response['token'] = $chef->createToken(config('app.name') . '_personal_access_token', ['chef'])->accessToken;
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
      'phone' => 'sometimes|nullable|string|max:15|min:8|unique:users,phone,' . auth('chef')->user()->id,
      'place' => 'sometimes|nullable|alpha_dash|exists:places,id',
      'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);
    try {
      $chef = Chef::where('id', Auth('chef')->user()->id)->firstOrFail();
      //adding images
      if ($request->hasFile('avatar') && $request->avatar != null) {
        $image = Image::make($request->file('avatar'))->encode('jpg', 1);
        if (Auth('chef')->user()->avatar != null) {
          if (File::exists("images/chefs/" . Auth('chef')->user()->avatar)) {
            File::delete("images/chefs/" . Auth('chef')->user()->avatar);
          }
        }
        if (!File::isDirectory(public_path("images/chefs/"))) {
          File::makeDirectory(public_path("images/chefs"));
        }
        $img_name = sprintf("CHEF%s.jpg", strtolower(Str::random(15)));
        $image->save(public_path("images/chefs/") . $img_name, 70, 'jpg');
        $request->avatar = $img_name;
        $chef->avatar = $img_name;
      }
      $attribs = [
        'name',
        'phone',
        'place',
        'password'
      ];
      foreach ($attribs as $attrib) {
        if ($request->has($attrib) && $request->{$attrib} != (null || '')) {
          if ($attrib == 'dob') {
            $chef->{$attrib} = Carbon::parse($request->{$attrib});
          } elseif ($attrib == 'place') {
            $chef->{$attrib . '_id'} = $request->{$attrib};
          } else {
            $chef->{$attrib} = $request->{$attrib};
          }
        }
      }
      $chef->update();

      $response['status'] = 'success';
      $response['message'] = 'Profile has been updated';
      $response['chef'] = $chef;
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
    Auth::guard('chef')->token()->revoke();
    Auth::guard('chef')->logout();
    $response['status'] = 'success';
    $response['message'] = 'Dispatcher Logged Out';
    return response()->json($response, Response::HTTP_OK);
  }

  public function list_chef_notifications()
  {
    $chef = Chef::find(Auth('chef')->user()->id)->first();
    $notifications = $chef->unreadNotifications()->paginate(20);
    $response['status'] = 'success';
    $response['notifications'] = $notifications;
    return response()->json($response, Response::HTTP_OK);
  }

  public function mark_chef_notification_as_read($notification_id)
  {
    $chef = Chef::find(Auth('chef')->user()->id)->first();
    $notification = $chef->notifications()->whereId($notification_id)->first();
    $notification->markAsRead();
    $response['status'] = 'success';
    $response['messages'] = 'Notification marked as Read';
    return response()->json($response, Response::HTTP_OK);
  }

  public function mark_chef_all_notification_as_read()
  {
    $chef = Dispatcher::find(Auth('chef')->user()->id)->first();
    $chef->unreadNotifications()->update(['read_at' => now()]);
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

    $chef = Chef::whereId(auth('chef')->user()->id)->firstOrFail();
    if (password_verify($request->input('current_password'), $chef->password)) {
      $chef->password = Hash::make($request->input('new_password'));
      $chef->update;
      $this->auth_success($chef);
      $response['status'] = 'success';
      $response['message'] = 'Password Change Successfull';
      $response['token'] = $chef->createToken(config('app.name') . '_personal_access_token', ['chef'])->accessToken;
      return response()->json($response, Response::HTTP_OK);
    } else {
      $response['message'] = 'Invalid Credentials';
      $response['errors'] = ['current_password' => ['Current Password is not correct']];
      return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }

  protected function auth_success($chef)
  {
    $chef->update([
      'last_login' => now()->format('Y-m-d H:i:s.u'),
      'last_ip' => request()->getClientIp(),
    ]);
    return;
  }
}
