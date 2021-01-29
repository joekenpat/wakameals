<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\ReservationRequestReceived;
use App\Models\Dispatcher;
use App\Models\TableReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

  public function store(Request $request)
  {
    $this->validate($request, [
      'dispatcher' => 'required|uuid|exists:dispatchers,id',
      'reserved_date' => 'date_format:Y-m-d|before_or_equal:2 weeks|after_or_equal:tomorrow',
      'reserved_time' => 'date_format:H:i|before_or_equal:17:00|after_or_equal:8:00',
      'number_of_seat' => 'required|integer|min:1|max:50',
    ]);

    $dispatcher = Dispatcher::whereId($request->dispatcher)->firstOrFail();
    $new_reservation = TableReservation::create([
      'user_id' => auth('user')->user()->id,
      'status' => 'pending',
      'seat_quantity' => $request->number_of_seat,
      'dispatcher_id' => $dispatcher->id,
      'place_id' => $dispatcher->place->id,
      'reserved_at' => Carbon::parse("{$request->reserved_date}")->startOfDay()->setTimeFrom(Carbon::parse("{$request->reserved_time}"))
    ]);
    foreach (['wdcebenezer@gmail.com', 'joekenpat@gmail.com'] as $recipient) {
      Mail::to($recipient)->send(new ReservationRequestReceived(auth('user')->user(), $new_reservation));
    }
    $response['status'] = 'success';
    $response['reservation'] = $new_reservation;
    return response()->json($response, Response::HTTP_OK);
  }
}
