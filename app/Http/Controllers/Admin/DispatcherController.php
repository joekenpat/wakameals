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
  public function index_pending()
  {
    $dispatchers = Dispatcher::with(['state', 'lga', 'town'])
      ->whereStatus('pending')->paginate(20)->makeHidden([
        'last_ip', 'created_at',
        'updated_at', 'deleted_at',
        'email_verified_at',
      ]);
    $response['status'] = 'success';
    $response['dispatchers'] = $dispatchers;
    return response()->json($response, Response::HTTP_OK);
  }


  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_active()
  {
    $dispatchers = Dispatcher::with(['state', 'lga', 'town'])
      ->whereStatus('active')
      ->paginate(20)
      ->makeHidden([
        'last_ip', 'created_at',
        'updated_at', 'deleted_at',
        'blocked_at', 'email_verified_at',
      ]);
    $response['status'] = 'success';
    $response['dispatchers'] = $dispatchers;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_blocked()
  {
    $dispatchers = Dispatcher::with(['state', 'lga', 'town'])
      ->whereStatus('blocked')->paginate(20)
      ->makeHidden([
        'last_ip', 'created_at',
        'updated_at', 'deleted_at',
        'blocked_at', 'email_verified_at',
      ]);
    $response['status'] = 'success';
    $response['dispatchers'] = $dispatchers;
    return response()->json($response, Response::HTTP_OK);
  }

  public function activate($dispatcher_code)
  {
    $dispatcher = Dispatcher::whereCode($dispatcher_code)->firstOrFail();
    $dispatcher->status = 'active';
    $dispatcher->update();
    $response['status'] = 'success';
    $response['message'] = $dispatcher->name . ' Dispatcher Account has been Activated';
    return response()->json($response, Response::HTTP_OK);
  }

  public function block($dispatcher_code)
  {
    $dispatcher = Dispatcher::whereCode($dispatcher_code)->firstOrFail();
    $dispatcher->status = 'block';
    $dispatcher->update();
    $response['status'] = 'success';
    $response['message'] = $dispatcher->name . ' Dispatcher Account has been Blocked';
    return response()->json($response, Response::HTTP_OK);
  }

  public function delete($dispatcher_code)
  {
    $dispatcher = Dispatcher::whereCode($dispatcher_code)->whereStatus('pending')->firstOrFail();
    $dispatcher->delete();
    $response['status'] = 'success';
    $response['message'] = $dispatcher->name . ' Dispatcher Account has been Deleted';
    return response()->json($response, Response::HTTP_OK);
  }
}
