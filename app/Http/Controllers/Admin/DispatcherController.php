<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatcher;
use App\Services\DispatcherSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DispatcherController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $dispatchers = DispatcherSearch::apply($request, 20);
    $response['status'] = 'success';
    $response['dispatchers'] = $dispatchers;
    return response()->json($response, Response::HTTP_OK);
  }

  public function activate($dispatcher_code)
  {
    $dispatcher = Dispatcher::whereCode($dispatcher_code)->firstOrFail();
    $dispatcher->activate();
    $response['status'] = 'success';
    $response['message'] = $dispatcher->name . ' Dispatcher Account has been Activated';
    return response()->json($response, Response::HTTP_OK);
  }

  public function block($dispatcher_code)
  {
    $dispatcher = Dispatcher::whereCode($dispatcher_code)->firstOrFail();
    $dispatcher->block();
    $response['status'] = 'success';
    $response['message'] = $dispatcher->name . ' Dispatcher Account has been Blocked';
    return response()->json($response, Response::HTTP_OK);
  }

  public function delete($dispatcher_code)
  {
    $dispatcher = Dispatcher::whereCode($dispatcher_code)->firstOrFail();
    $dispatcher->delete();
    $response['status'] = 'success';
    $response['message'] = $dispatcher->name . ' Dispatcher Account has been Deleted';
    return response()->json($response, Response::HTTP_OK);
  }
}
