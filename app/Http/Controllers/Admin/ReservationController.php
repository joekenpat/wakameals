<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReservationApproved;
use App\Mail\ReservationRejected;
use App\Models\Reservation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_pending()
  {
    $reservations = Reservation::where('status', 'pending')->paginate(20);
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
    $reservations = Reservation::where('status', 'cancelled')->paginate(20);
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
    $reservations = Reservation::where('status', 'approved')->paginate(20);
    $response['status'] = 'success';
    $response['reservations'] = $reservations;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_closed()
  {
    $reservations = Reservation::where('status', 'close')->paginate(20);
    $response['status'] = 'success';
    $response['reservations'] = $reservations;
    return response()->json($response, Response::HTTP_OK);
  }

  public function cancel($reservation_code)
  {
    $reservation = Reservation::whereCode($reservation_code)
      ->firstOrFail();
    if ($reservation->status == 'cancelled') {
      $response['status'] = 'success';
      $response['message'] = $reservation->code . ' Table Reservation has been Cancelled Already';
    } else {
      $reservation->status = 'cancelled';
      $reservation->update();
      Mail::to($reservation->user)->send(new ReservationRejected($reservation->user, $reservation));
      $response['status'] = 'success';
      $response['message'] = $reservation->code . ' Table Reservation has been Cancelled';
    }
    return response()->json($response, Response::HTTP_OK);
  }

  public function approve($reservation_code)
  {
    $reservation = Reservation::whereCode($reservation_code)
      ->firstOrFail();
    if ($reservation->status == 'approve') {
      $response['status'] = 'success';
      $response['message'] = $reservation->code . ' Table Reservation has Approved Already';
    } else {
      $reservation->status = 'approved';
      $reservation->update();
      Mail::to($reservation->user)->send(new ReservationApproved($reservation->user, $reservation));
      $response['status'] = 'success';
      $response['message'] = $reservation->code . ' Table Reservation has been Approved';
    }
    return response()->json($response, Response::HTTP_OK);
  }

  public function close($reservation_code)
  {
    $reservation = Reservation::whereCode($reservation_code)
      ->firstOrFail();
    if ($reservation->status == 'closed') {
      $response['status'] = 'success';
      $response['message'] = $reservation->code . ' Table Reservation has been Closed Earlier';
    } else {
      $reservation->status = 'closed';
      $reservation->update();
      $response['status'] = 'success';
      $response['message'] = $reservation->code . ' Table Reservation has been Approved';
    }

    return response()->json($response, Response::HTTP_OK);
  }

  public function delete($reservation_code)
  {
    $reservation = Reservation::whereCode($reservation_code)
      ->firstOrFail();
    $reservation->delete();
    $response['status'] = 'success';
    $response['message'] = $reservation_code . ' Table Reservation has been Deleted';
    return response()->json($response, Response::HTTP_OK);
  }
}
