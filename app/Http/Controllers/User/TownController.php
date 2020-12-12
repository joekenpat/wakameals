<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TownSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TownController extends Controller
{
  public function index(Request $request)
  {
    $towns = TownSearch::apply($request, 20);
    $response['status'] = 'success';
    $response['towns'] = $towns;
    return response()->json($response, Response::HTTP_OK);
  }
}
