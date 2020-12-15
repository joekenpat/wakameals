<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\MealSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MealController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $meals = MealSearch::apply($request, 20);
    $response['status'] = 'success';
    $response['meals'] = $meals;
    return response()->json($response, Response::HTTP_OK);
  }
}
