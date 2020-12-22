<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_active()
  {
    $user = User::whereStatus('active')->paginate(20);
    $response['status'] = 'success';
    $response['users'] = $user;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_blocked(Request $request)
  {
    $user = User::whereStatus('active')->paginate(20);
    $response['status'] = 'success';
    $response['users'] = $user;
    return response()->json($response, Response::HTTP_OK);
  }

  public function activate($user_id)
  {
    $user = User::find($user_id)->firstOrFail();
    $user->status = 'active';
    $user->blocked_at = null;
    $user->update();
    $response['status'] = 'success';
    $response['message'] = $user->first_name . ' User Account has been Activated';
    return response()->json($response, Response::HTTP_OK);
  }

  public function block($user_id)
  {
    $user = User::find($user_id)->firstOrFail();
    $user->status = 'blocked';
    $user->blocked_at = now();
    $user->update();
    $response['status'] = 'success';
    $response['message'] = $user->first_name . ' User Account has been Blocked';
    return response()->json($response, Response::HTTP_OK);
  }

  public function delete($user_id)
  {
    $user = User::find($user_id)->firstOrFail();
    $user->delete();
    $response['status'] = 'success';
    $response['message'] = $user->first_name . ' User Account has been Deleted';
    return response()->json($response, Response::HTTP_OK);
  }
}
