<?php

namespace App\Http\Controllers\User;

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
  public function index(Request $request)
  {
    $places = Place::whereEnabled(true)->get();
    $response['status'] = 'success';
    $response['places'] = $places;
    return response()->json($response, Response::HTTP_OK);
  }
}
