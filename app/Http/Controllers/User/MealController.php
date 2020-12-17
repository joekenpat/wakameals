<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Response;

class MealController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = Category::all();
    $response['status'] = 'success';
    $response['data'] = $data;
    return response()->json($response, Response::HTTP_OK);
  }
}
