<?php

namespace App\Http\Controllers\Dispatcher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class ReservationController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_pending()
  {
    $reservations = auth('user')->user()->pending_reservations()->paginate(20);
    $response['status'] = 'success';
    $response['reservations'] = $reservations;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_cancelled()
  {
    $reservations = auth('user')->user()->cancelled_reservations()->paginate(20);
    $response['status'] = 'success';
    $response['reservations'] = $reservations;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_approved()
  {
    $reservations = auth('user')->user()->approved_reservations()->paginate(20);
    $response['status'] = 'success';
    $response['reservations'] = $reservations;
    return response()->json($response, Response::HTTP_OK);
  }
}
