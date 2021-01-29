<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlaceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_enabled()
  {
    $places = Place::whereEnabled(true)->get();
    $response['status'] = 'success';
    $response['places'] = $places;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_disabled()
  {
    $places = Place::whereEnabled(false)->get();
    $response['status'] = 'success';
    $response['places'] = $places;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|string|min:3|max:245'
    ]);


    $place = Place::create([
      'name' => $request->name,
      'enabled' => true,
    ]);

    $response['status'] = 'success';
    $response['place'] = $place;
    return response()->json($response, Response::HTTP_OK);
  }


  public function enable($place_slug)
  {
    $place = Place::whereSlug($place_slug)->firstOrFail();
    $place->enable();
    $response['status'] = 'success';
    $response['message'] = $place->name . ' Place has been enabled for delivery';
    return response()->json($response, Response::HTTP_OK);
  }

  public function disable($place_slug)
  {
    $place = Place::whereSlug($place_slug)->firstOrFail();
    $place->disable();
    $response['status'] = 'success';
    $response['message'] = $place->name . ' State has been disabled for delivery';
    return response()->json($response, Response::HTTP_OK);
  }
}
