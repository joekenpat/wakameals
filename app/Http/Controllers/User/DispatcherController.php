<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Dispatcher;
use App\Models\Lga;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class DispatcherController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($lga_slug)
  {
    try {
      $lga =  Lga::whereSlug($lga_slug)->firstOrFail();
      $pickups = Dispatcher::whereLgaId($lga->id)->whereType("pickup")->get();
      $response['status'] = 'success';
      $response['pickup_locations'] = $pickups;
      return response()->json($response, Response::HTTP_OK);
    } catch (ModelNotFoundException $mnf) {
      $response['status'] = 'error';
      $response['message'] = 'No such lga';
      return response()->json($response, Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
