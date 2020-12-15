<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Services\StateSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StateController extends Controller
{/**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $states = StateSearch::apply($request, null);
    $response['status'] = 'success';
    $response['states'] = $states;
    return response()->json($response, Response::HTTP_OK);
  }
}
