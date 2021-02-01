<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Chef;
use App\Models\PasswordReset;
use App\Notifications\ChefPasswordResetCodeSent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
  public function password_reset_request(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email|exists:chefs',
    ], [
      'email.exists' => 'No Account With Found for :input'
    ]);
    try {
      $chef = Chef::whereEmail($request->email)->firstOrFail();
      $new_password_reset = new PasswordReset([
        'used' => false,
        'expires_at' => now()->addMinutes(30)
      ]);
      $new_password_reset->resetable()->associate($chef);
      $new_password_reset->save();
      $chef->notify(new ChefPasswordResetCodeSent($chef, $new_password_reset));
      $response['status'] = 'success';
      $response['message'] = 'A reset code has been sent to email, please follow the instructions there.';
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
  public function validate_password_reset_token(Request $request)
  {
    $this->validate($request, [
      'id' => 'required|string',
      'token' => 'required|string',
    ]);

    $identity['email'] = Crypt::decryptString($request->id);
    $validator = Validator::make($identity, [
      'email' => 'required|email|exists:chefs'
    ]);
    if (!$validator->fails()) {
      $chef = Chef::whereEmail($identity['email'])->firstOrFail();
      $credentials['code'] = Crypt::decryptString($request->token);
      $validator = Validator::make($credentials, [
        'code' => 'required|alpha_num|exists:password_resets'
      ]);
      if (!$validator->fails()) {

        $password_reset = $chef->password_resets()->where('code',$credentials['code'])->whereUsed(false)->firstOrFail();
        if (!now()->greaterThan($password_reset->expires_at)) {
          $response['status'] = 'success';
          $response['message'] = 'You can now enter a new password';
          $response['email'] = $identity['email'];
          $response['code'] = $credentials['code'];
          return response()->json($response, Response::HTTP_OK);
        } else {
          $response['status'] = 'error';
          $response['message'] = 'Reset Token Expired!';
          return response()->json($response, Response::HTTP_BAD_REQUEST);
        }
      } else {
        $response['status'] = 'error';
        $response['message'] = 'Token Invalid for dispatcher';
        return response()->json($response, Response::HTTP_BAD_REQUEST);
      }
    } else {
      $response['status'] = 'error';
      $response['message'] = 'No account with email :input';
      return response()->json($response, Response::HTTP_BAD_REQUEST);
    }
  }

  public function password_reset(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email|exists:chefs',
      'code' => 'required|alpha_num|exists:password_resets',
      'new_password' => 'required|string|min:5|max:15',
      're_password' => 'required|same:new_password',
    ]);

    $chef = Chef::whereEmail($request->email)->firstOrFail();
    $chef_password_reset = $chef->password_resets()->whereCode($request->code)->whereUsed(false)->firstOrFail();
    if (!now()->greaterThan($chef_password_reset->expires_at)) {
      $chef->password = Hash::make($request->new_password);
      $chef->update();
      $chef_password_reset->used = true;
      $chef_password_reset->update();
      $response['status'] = 'success';
      $response['message'] = 'Password Reset Successful';
      return response()->json($response, Response::HTTP_OK);
    } else {
      $response['status'] = 'error';
      $response['message'] = 'Reset Token Expired!';
      return response()->json($response, Response::HTTP_BAD_REQUEST);
    }
  }
}
