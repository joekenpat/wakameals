<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use App\Models\State;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class LgaController extends Controller
{
  public function index($state_slug)
  {
    $rules = [
      'state' => 'required|alpha_dash|exists:states,slug',
    ];
    $valid_value = ['state' => $state_slug];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      $state = State::whereSlug($state_slug)->firstOrFail();
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

  public function enable($lga_slug)
  {
    $lga = Lga::whereSlug($lga_slug)->firstOrFail();
    $lga->enable();
    $response['status'] = 'success';
    $response['message'] = $lga->name . ' LGA and it\'s respective towns has been enabled for delivery';
    return response()->json($response, Response::HTTP_OK);
  }

  public function disable($lga_slug)
  {
    $lga = Lga::whereSlug($lga_slug)->firstOrFail();
    $lga->disable();
    $response['status'] = 'success';
    $response['message'] = $lga->name . ' LGA and it\'s respective towns has been disabled for delivery';
    return response()->json($response, Response::HTTP_OK);
  }
}
