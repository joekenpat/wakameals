<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class TableReservationController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_pending()
  {
    $table_reservations = auth('user')->user()->pending_table_reservations()->paginate(20);
    $response['status'] = 'success';
    $response['table_reservations'] = $table_reservations;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_cancelled()
  {
    $table_reservations = auth('user')->user()->cancelled_table_reservations()->paginate(20);
    $response['status'] = 'success';
    $response['table_reservations'] = $table_reservations;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_approved()
  {
    $table_reservations = auth('user')->user()->approved_table_reservations()->paginate(20);
    $response['status'] = 'success';
    $response['table_reservations'] = $table_reservations;
    return response()->json($response, Response::HTTP_OK);
  }
}
