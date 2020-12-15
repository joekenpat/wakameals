<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubcategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $subcategories = Subcategory::get();
    $response['status'] = 'success';
    $response['subcategories'] = $subcategories;
    return response()->json($response, Response::HTTP_OK);
  }
}
