<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatcher;
use App\Models\Place;
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
    $dispatchers = Dispatcher::with(['place'])
      ->whereStatus('pending')->paginate(20);
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
    $dispatchers = Dispatcher::with(['place'])
      ->whereStatus('active')
      ->paginate(20);
    $response['status'] = 'success';
    $response['dispatchers'] = $dispatchers;
    return response()->json($response, Response::HTTP_OK);
  }

  /* Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index_by_place($place_id)
  {
    try {
      // $lga =  Lga::whereSlug($lga_slug)->firstOrFail();
      // $pickups = Dispatcher::whereLgaId($lga->id)->whereType("pickup")->get();
      $place = Place::whereId($place_id)->firstOrFail();
      $pickups = Dispatcher::wherePlaceId($place->id)->with(['place'])->select(['id', 'code', 'name', 'place_id', 'address'])
        ->whereType("pickup")
        ->whereStatus('active')->get();
      $response['status'] = 'success';
      $response['pickup_places'] = $pickups;
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_blocked()
  {
    $dispatchers = Dispatcher::with(['place'])
      ->whereStatus('blocked')->paginate(20);
    $response['status'] = 'success';
    $response['dispatchers'] = $dispatchers;
    return response()->json($response, Response::HTTP_OK);
  }

  public function activate($dispatcher_code)
  {
    $dispatcher = Dispatcher::whereCode($dispatcher_code)->firstOrFail();
    if ($dispatcher->status == "active") {
      $response['status'] = 'success';
      $response['message'] = $dispatcher->name . ' Dispatcher Account is Already Active ';
    } else {
      $dispatcher->status = 'active';
      $dispatcher->blocked_at = null;
      $dispatcher->update();
      $response['status'] = 'success';
      $response['message'] = $dispatcher->name . ' Dispatcher Account has been Activated';
    }
    return response()->json($response, Response::HTTP_OK);
  }

  public function block($dispatcher_code)
  {
    $dispatcher = Dispatcher::whereCode($dispatcher_code)->firstOrFail();
    if ($dispatcher->status == "blocked") {
      $response['status'] = 'success';
      $response['message'] = $dispatcher->name . ' Dispatcher Account is Already Blocked ';
    } else {
      $dispatcher->status = 'blocked';
      $dispatcher->blocked_at = now();
      $dispatcher->update();
      $response['status'] = 'success';
      $response['message'] = $dispatcher->name . ' Dispatcher Account has been Blocked';
    }
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
