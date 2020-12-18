<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatcher;
use App\Models\Order;
use App\Notifications\CancelledOrder;
use App\Notifications\CompletedOrder;
use App\Notifications\ConfirmedOrder;
use App\Notifications\DispatchedOrder;
use App\Services\OrderSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $orders = OrderSearch::apply($request, 20);
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
      'dispatcher_code' => 'required_if:dispatch_type,door_delivery|alpha_num|size:8|exists:dispatchers,code',
    ]);
    if ($request->new_status == 'completed') {
      $order = Order::find($request->order_id);
      $order->status = 'completed';
      $order->update();
      $order->user()->notify(new CompletedOrder($order->user(), $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been Completed';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'cancelled') {
      $order = Order::find($request->order_id);
      $order->status = 'cancelled';
      $order->update();
      $order->user()->notify(new CancelledOrder($order->user(), $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been cancelled';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'dispatched') {

      $order = Order::find($request->order_id);
      if ($request->dispatch_type == 'pickup') {
        $dispatcher = Dispatcher::whereId($order->dispatcher_id)->firstOrFail();
      } else {
        $dispatcher = Dispatcher::whereCode($request->dispatcher_code)->firstOrFail();
      }
      $order->dispatcher_code = $dispatcher->code;
      $order->status = 'dispatched';
      $order->update();
      $order->user()->notify(new DispatchedOrder($order->user(), $order, $dispatcher));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been Dispatched';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'confirmed') {
      $order = Order::find($request->order_id);
      $order->status = 'confirmed';
      $order->update();
      $order->user()->notify(new ConfirmedOrder($order->user(), $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been Confirmed';
      return response()->json($response, Response::HTTP_OK);
    }
  }
}
