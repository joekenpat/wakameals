<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class AdminController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * User Default login
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
      'email' => 'sometimes|string|exists:users,' . $auth_by,
    ], $messages);

    if (Admin::where("$auth_by", $request->input('identifier'))->exists()) {
      $user = Admin::where($auth_by, $request->input('identifier'))->first();
      if (password_verify($request->input('password'), $user->password)) {
        $this->auth_success($user);
        $response['status'] = 'success';
        $response['message'] = 'Log-in Successfull';
        $response['token'] = $user->createToken(config('app.name') . '_personal_access_token', ['admin'])->accessToken;
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
   * Update Admin.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    $this->validate($request, [
      'first_name' => 'sometimes|nullable|alpha|max:25|min:2',
      'last_name' => 'sometimes|nullable|alpha|max:25|min:2',
      'phone' => 'sometimes|nullable|string|max:15|min:8',
      'place' => 'sometimes|nullable|alpha_dash|exists:places,id',
      'state' => 'sometimes|nullable|alpha_dash|exists:states,id',
      'lga' => 'sometimes|nullable|alpha_dash|exists:lgas,id',
      'address' => 'sometimes|nullable|string',
      'address' => 'sometimes|nullable|string|min:5|max:255',
      'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);
    try {
      $admin = Admin::where('id', Auth('admin')->user()->id)->firstOrFail();
      //adding images
      if ($request->hasFile('avatar') && $request->avatar != null) {
        $image = Image::make($request->file('avatar'))->encode('jpg', 1);
        if (Auth('admin')->user()->avatar != null) {
          if (File::exists("images/users/" . Auth('admin')->user()->avatar)) {
            File::delete("images/users/" . Auth('admin')->user()->avatar);
          }
        }
        $img_name = sprintf("%s%s.jpg", strtolower(Str::random(15)));
        $image->save(public_path("images/users/") . $img_name, 70, 'jpg');
        $request->avatar = $img_name;
        $admin->avatar = $img_name;
      }
      $attribs = [
        'first_name',
        'last_name',
        'title',
        'phone',
        'state',
        'lga',
        'town',
        'email',
        'password'
      ];
      foreach ($attribs as $attrib) {
        if ($request->has($attrib) && $request->{$attrib} != (null || '')) {
          if ($attrib == 'dob') {
            $admin->{$attrib} = Carbon::parse($request->{$attrib});
          } elseif (in_array($attrib, ['state', 'town', 'lga'])) {
            $admin->{$attrib . '_id'} = $request->{$attrib};
          } else {
            $admin->{$attrib} = $request->{$attrib};
          }
        }
      }
      $admin->update();

      $response['status'] = 'success';
      $response['message'] = 'Profile has been updated';
      $response['user'] = $admin;
      return response()->json($response, Response::HTTP_OK);
    } catch (ModelNotFoundException $mnt) {
      $response['status'] = 'error';
      $response['message'] = 'Admin not Found';
      return response()->json($response, Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function logout()
  {
    Auth::guard('admin')->token()->revoke();
    $response['status'] = 'success';
    $response['message'] = 'Admin Logged Out';
    return response()->json($response, Response::HTTP_OK);
  }

  public function list_admin_notifications()
  {
    $admin = Admin::find(Auth('admin')->user()->id)->first();
    $notifications = $admin->unreadNotifications()->paginate(20);
    $response['status'] = 'success';
    $response['notifications'] = $notifications;
    return response()->json($response, Response::HTTP_OK);
  }

  public function mark_admin_notification_as_read($notification_id)
  {
    $admin = Admin::find(Auth('admin')->user()->id)->first();
    $notification = $admin->notifications()->whereId($notification_id)->first();
    $notification->markAsRead();
    $response['status'] = 'success';
    $response['messages'] = 'Notification marked as Read';
    return response()->json($response, Response::HTTP_OK);
  }

  public function mark_admin_all_notification_as_read()
  {
    $admin = Admin::find(Auth('admin')->user()->id)->first();
    $admin->unreadNotifications()->update(['read_at' => now()]);
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

    $admin = Admin::find(Auth::id());
    $credentials = [
      "email" => $admin->email,
      'password' => $request->input('current_password'),
    ];

    if (Auth::guard('web')->attempt($credentials)) {
      $admin->password = Hash::make($request->input('new_password'));
      $admin->update;
      $this->auth_success($admin);
      $response['status'] = 'success';
      $response['message'] = 'Password Change Successfull';
      $response['token'] = $admin->createToken(config('app.name') . '_personal_access_token', ['admin'])->accessToken;
      return response()->json($response, Response::HTTP_OK);
    } else {
      $response['message'] = 'Invalid Credentials';
      $response['errors'] = ['current_password' => ['Current Password Incorrect']];
      return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }

  protected function auth_success($admin)
  {
    $admin->update([
      'last_login' => now()->format('Y-m-d H:i:s.u'),
      'last_ip' => request()->getClientIp(),
    ]);
    return;
  }
}
