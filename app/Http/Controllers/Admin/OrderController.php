<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderCompleted;
use App\Mail\OrderConfirmed;
use App\Mail\OrderSystemCancelled;
use App\Models\Dispatcher;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($status)
  {
    $statuses = [];
    if (in_array($status, ['new', 'confirmed', 'dispatched', 'completed'])) {
      $statuses = [$status];
    } elseif ($status == 'cancelled') {
      $statuses = ['cancelled', 'cancelled_failed_payment', 'cancelled_system', 'cancelled_user'];
    } else {
      $statuses = ['new'];
    }

    $orders = Order::with(['user'])->whereIn('status', $statuses)->paginate(20);
    $response['status'] = 'success';
    $response['orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }


  public function change_status(Request $request)
  {
    $this->validate($request, [
      'order_id' => 'required|uuid',
      'new_status' => 'required|alpha|in:completed,dispatched,confirmed,cancelled',
      'dispatch_type' => 'required_if:new_status,dispatched|in:pickup,door_delivery',
      'dispatcher_code' => 'required_if:dispatch_type,door_delivery|alpha_num|size:6|exists:dispatchers,code',
    ]);
    if ($request->new_status == 'completed') {
      $order = Order::with(['user'])->with('ordered_meals')->whereId($request->order_id)->firstOrFail();
      $order->status = 'completed';
      $order->update();
      $order_user = User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderCompleted($order_user, $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been Completed';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'cancelled') {
      $order = Order::with('ordered_meals')->whereId($request->order_id)->firstOrFail();
      $order->status = 'cancelled';
      $order->update();
      $order_user =User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderSystemCancelled($order_user, $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been cancelled';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'dispatched') {

      $order = Order::with('ordered_meals')->whereId($request->order_id)->firstOrFail();
      if ($request->dispatch_type == 'pickup') {
        $dispatcher = Dispatcher::whereId($order->dispatcher_id)->firstOrFail();
      } else {
        $dispatcher = Dispatcher::whereCode($request->dispatcher_code)->firstOrFail();
      }
      $order->dispatcher_code = $dispatcher->code;
      $order->status = 'dispatched';
      $order->update();
      $order_user =User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderCompleted($order_user, $order, $dispatcher));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been Dispatched';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'confirmed') {
      $order = Order::with('ordered_meals')->whereId($request->order_id)->firstOrFail();
      $order->status = 'confirmed';
      $order->update();
      $order_user =User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderConfirmed($order_user, $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been Confirmed';
      return response()->json($response, Response::HTTP_OK);
    }
  }
}
