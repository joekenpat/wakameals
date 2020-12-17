<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TownController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($lga_slug)
  {
    $rules = [
      'lga' => 'required|alpha_dash|exists:lgas,slug',
    ];
    $valid_value = ['lga' => $lga_slug];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      $lga = Lga::whereSlug($lga_slug)->firstOrFail();
      $towns = Town::whereLgaId($lga->id)->get();
      $response['status'] = 'success';
      $response['towns'] = $towns;
      return response()->json($response, Response::HTTP_OK);
    } else {
      $response['status'] = 'success';
      $response['message'] = 'Invalid LGA';
      return response()->json($response, Response::HTTP_OK);
    }
  }
}
