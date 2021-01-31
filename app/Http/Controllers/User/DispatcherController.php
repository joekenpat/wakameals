<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Dispatcher;
use App\Models\Place;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class DispatcherController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($place_slug)
  {
    try {
      // $lga =  Lga::whereSlug($lga_slug)->firstOrFail();
      // $pickups = Dispatcher::whereLgaId($lga->id)->whereType("pickup")->get();
      $place = Place::whereSlug($place_slug)->firstOrFail();
      $pickups = Dispatcher::wherePlaceId($place->id)->with(['place'])->select(['id', 'code', 'name', 'place_id', 'address'])
        ->whereType("pickup")
        ->whereStatus('active')->get();
      $response['status'] = 'success';
      $response['pickup_locations'] = $pickups;
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
