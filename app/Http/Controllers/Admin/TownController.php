<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Town;
use App\Services\TownSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TownController extends Controller
{
   /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $towns = TownSearch::apply($request, 20);
    $response['status'] = 'success';
    $response['towns'] = $towns;
    return response()->json($response, Response::HTTP_OK);
  }
}
