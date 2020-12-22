<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class LgaController extends Controller
{
  public function index($state_id)
  {
    $rules = [
      'id' => 'required|alpha_dash|exists:states,id',
    ];
    $valid_value = ['id' => $state_id];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      $state = State::whereId($state_id)->firstOrFail();
      $lgas = Lga::whereStateId($state->id)->get();
      $response['status'] = 'success';
      $response['lgas'] = $lgas;
      return response()->json($response, Response::HTTP_OK);
    } else {
      $response['status'] = 'success';
      $response['message'] = 'Invalid State';
      return response()->json($response, Response::HTTP_OK);
    }
  }
}
