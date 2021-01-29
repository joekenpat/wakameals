<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReservationApproved;
use App\Mail\ReservationRejected;
use App\Models\TableReservation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class TableReservationController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_pending()
  {
    $table_reservations = TableReservation::where('status', 'pending')->paginate(20);
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
    $table_reservations = TableReservation::where('status', 'cancelled')->paginate(20);
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
    $table_reservations = TableReservation::where('status', 'approved')->paginate(20);
    $response['status'] = 'success';
    $response['table_reservations'] = $table_reservations;
    return response()->json($response, Response::HTTP_OK);
  }

  public function cancel($reservation_code)
  {
    $reservation = TableReservation::whereCode($reservation_code)
      ->firstOrFail();
    $reservation->status = 'cancelled';
    $reservation->update();
    Mail::to($reservation->user)->send(new ReservationRejected($reservation->user, $reservation));
    $response['status'] = 'success';
    $response['message'] = $reservation->code . ' Table Reservation has been Cancelled';
    return response()->json($response, Response::HTTP_OK);
  }

  public function approve($reservation_code)
  {
    $reservation = TableReservation::whereCode($reservation_code)
      ->firstOrFail();
    $reservation->status = 'approved';
    $reservation->update();
    Mail::to($reservation->user)->send(new ReservationApproved($reservation->user, $reservation));
    $response['status'] = 'success';
    $response['message'] = $reservation->code . ' Table Reservation has been Approved';
    return response()->json($response, Response::HTTP_OK);
  }

  public function delete($reservation_code)
  {
    $reservation = TableReservation::whereCode($reservation_code)
      ->firstOrFail();
    $reservation->delete();
    $response['status'] = 'success';
    $response['message'] = $reservation_code . ' Table Reservation has been Deleted';
    return response()->json($response, Response::HTTP_OK);
  }
}
