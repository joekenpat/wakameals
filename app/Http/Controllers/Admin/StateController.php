<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StateController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $states = State::get();
    $response['status'] = 'success';
    $response['states'] = $states;
    return response()->json($response, Response::HTTP_OK);
  }

  public function enable($state_slug)
  {
    $state = State::whereSlug($state_slug)->firstOrFail();
    $state->enable();
    $response['status'] = 'success';
    $response['message'] = $state->name . ' State and it\'s respective lgas & towns has been enabled for delivery';
    return response()->json($response, Response::HTTP_OK);
  }

  public function disable($state_slug)
  {
    $state = State::whereSlug($state_slug)->firstOrFail();
    $state->disable();
    $response['status'] = 'success';
    $response['message'] = $state->name . ' State and it\'s respective lgas & towns has been disabled for delivery';
    return response()->json($response, Response::HTTP_OK);
  }
}
