<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use App\Models\Town;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TownController extends Controller
{
 /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($lga_id)
  {
    $rules = [
      'id' => 'required|alpha_dash|exists:lgas,id',
    ];
    $valid_value = ['id' => $lga_id];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      $lga = Lga::whereId($lga_id)->firstOrFail();
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
