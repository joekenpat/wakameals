<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\ReservationRequestReceived;
use App\Models\Admin;
use App\Models\Dispatcher;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{

  // /**
  //  * Display a listing of the resource.
  //  *
  //  * @return \Illuminate\Http\Response
  //  */
  // public function index_pending()
  // {
  //   $reservations = auth('user')->user()->pending_reservations()->paginate(20);
  //   $response['status'] = 'success';
  //   $response['reservations'] = $reservations;
  //   return response()->json($response, Response::HTTP_OK);
  // }

  // /**
  //  * Display a listing of the resource.
  //  *
  //  * @return \Illuminate\Http\Response
  //  */
  // public function index_cancelled()
  // {
  //   $reservations = auth('user')->user()->cancelled_reservations()->paginate(20);
  //   $response['status'] = 'success';
  //   $response['reservations'] = $reservations;
  //   return response()->json($response, Response::HTTP_OK);
  // }

  // /**
  //  * Display a listing of the resource.
  //  *
  //  * @return \Illuminate\Http\Response
  //  */
  // public function index_closed()
  // {
  //   $reservations = auth('user')->user()->closed_reservations()->paginate(20);
  //   $response['status'] = 'success';
  //   $response['reservations'] = $reservations;
  //   return response()->json($response, Response::HTTP_OK);
  // }

  // /**
  //  * Display a listing of the resource.
  //  *
  //  * @return \Illuminate\Http\Response
  //  */
  // public function index_approved()
  // {
  //   $reservations = auth('user')->user()->approved_reservations()->paginate(20);
  //   $response['status'] = 'success';
  //   $response['reservations'] = $reservations;
  //   return response()->json($response, Response::HTTP_OK);
  // }

  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|string|min:3|max:50',
      'email' => 'required|email',
      'address' => 'sometimes|string|nullable',
      'phone' => 'required|string|size:11',
      'event_address' => 'sometimes|string|nullable',
      'service_type' => 'required|string|in:pickup,door_delivery,full_buffet,served_buffet,pre_packed_service',
      'crowd_type' => 'required|string|in:mixed,adults,children,advanced,high_class,middle_class,low_class,mixed_class',
      'menu_type' => 'required|string|in:waka_g&b,waka_chinese,waka_naija,beverages,others',
      'dispatcher' => 'required|exists:dispatchers,id',
      'reserved_date' => 'date_format:Y-m-d|before_or_equal:2 weeks|after_or_equal:tomorrow',
      'reserved_time' => 'date_format:H:i|before_or_equal:17:00|after_or_equal:8:00',
      'no_of_persons' => 'required|integer|min:1|max:50',
    ]);

    $dispatcher = Dispatcher::whereId($request->dispatcher)->firstOrFail();
    $new_reservation = Reservation::create([
      'name' => $request->name,
      'email' => $request->email,
      'phone' => $request->phone,
      'address' => $request->address,
      'event_address' => $request->event_address,
      'event_type' => $request->event_type,
      'crowd_type' => $request->crowd_type,
      'menu_type' => $request->menu_type,
      'status' => 'pending',
      'service_type' => $request->service_type,
      'no_of_persons' => $request->no_of_persons,
      'dispatcher_id' => $dispatcher->id,
      'place_id' => $dispatcher->place->id,
      'reserved_at' => Carbon::parse("{$request->reserved_date}")->startOfDay()->setTimeFrom(Carbon::parse("{$request->reserved_time}"))
    ]);
    $reserver = [
      'name' => $request->name,
      'email' => $request->email,
      'phone' => $request->phone,
    ];
    $admins = Admin::where('place_id', $new_reservation->place_id)->get();

    foreach ($admins as $recipient) {
      Mail::to($recipient->email)->send(new ReservationRequestReceived($reserver, $new_reservation));
    }
    $response['status'] = 'success';
    $response['reservation'] = $new_reservation;
    return response()->json($response, Response::HTTP_OK);
  }
}
