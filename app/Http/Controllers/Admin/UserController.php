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
  public function index(Request $request)
  {
    $user = UserSearch::apply($request, 20);
    $response['status'] = 'success';
    $response['user'] = $user;
    return response()->json($response, Response::HTTP_OK);
  }

  public function activate($user_id)
  {
    $user = User::find($user_id)->firstOrFail();
    $user->activate();
    $response['status'] = 'success';
    $response['message'] = $user->name . ' User Account has been Activated';
    return response()->json($response, Response::HTTP_OK);
  }

  public function block($user_id)
  {
    $user = User::find($user_id)->firstOrFail();
    $user->block();
    $response['status'] = 'success';
    $response['message'] = $user->name . ' User Account has been Blocked';
    return response()->json($response, Response::HTTP_OK);
  }

  public function delete($user_id)
  {
    $user = User::find($user_id)->firstOrFail();
    $user->delete();
    $response['status'] = 'success';
    $response['message'] = $user->name . ' User Account has been Deleted';
    return response()->json($response, Response::HTTP_OK);
  }
}
