<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use App\Services\LgaSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LgaController extends Controller
{
  public function index(Request $request)
  {
    $lgas = LgaSearch::apply($request, null);
    $response['status'] = 'success';
    $response['lgas'] = $lgas;
    return response()->json($response, Response::HTTP_OK);
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
