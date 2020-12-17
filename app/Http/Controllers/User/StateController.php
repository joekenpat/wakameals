<?php

namespace App\Http\Controllers\User;

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
}
