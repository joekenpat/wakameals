<?php

namespace App\Http\Controllers\Dispatcher;

use App\Http\Controllers\Controller;
use App\Models\Chef;
use Illuminate\Http\Response;

class ChefController extends Controller
{
  /**
   * Display a resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_active()
  {
    $chefs = Chef::with(['place'])
      ->whereDispatcherId(auth('dispatcher')->user()->id)
      ->whereStatus('active')
      ->paginate(20);
    $response['status'] = 'success';
    $response['chefs'] = $chefs;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_blocked()
  {
    $chefs = Chef::with(['place'])
      ->whereDispatcherId(auth('dispatcher')->user()->id)
      ->whereStatus('blocked')
      ->paginate(20);
    $response['status'] = 'success';
    $response['chefs'] = $chefs;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_pending()
  {
    $chefs = Chef::with(['place'])
      ->whereDispatcherId(auth('dispatcher')->user()->id)
      ->whereStatus('pending')
      ->paginate(20);
    $response['status'] = 'success';
    $response['chefs'] = $chefs;
    return response()->json($response, Response::HTTP_OK);
  }

  public function block($chef_id)
  {
    $chef = Chef::whereId($chef_id)
      ->whereDispatcherId(auth('dispatcher')->user()->id)
      ->firstOrFail();
    if ($chef->status == 'blocked') {
      $response['status'] = 'success';
      $response['message'] = $chef->name . ' Chef Account is already Blocked';
    } else {
      $chef->status = 'blocked';
      $chef->blocked_at = now();
      $chef->update();
      $response['status'] = 'success';
      $response['message'] = $chef->name . ' Chef Account has been Blocked';
    }
    return response()->json($response, Response::HTTP_OK);
  }

  public function unblock($chef_id)
  {
    $chef = Chef::whereId($chef_id)
      ->whereDispatcherId(auth('dispatcher')->user()->id)
      ->firstOrFail();
    if ($chef->status == 'active') {
      $response['status'] = 'success';
      $response['message'] = $chef->name . ' Chef Account is already Active';
    } else {
      $chef->status = 'active';
      $chef->blocked_at = now();
      $chef->update();
      $response['status'] = 'success';
      $response['message'] = $chef->name . ' Chef Account is  now Active';
    }
    return response()->json($response, Response::HTTP_OK);
  }

  public function delete($chef_id)
  {
    $chef = Chef::whereId($chef_id)
      ->whereDispatcherId(auth('dispatcher')->user()->id)
      ->whereStatus('pending')->firstOrFail();
    $chef->delete();
    $chef['status'] = 'success';
    $response['message'] = $chef->name . ' Chef Account has been Deleted';
    return response()->json($response, Response::HTTP_OK);
  }
}
