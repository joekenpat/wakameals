<?php

namespace App\Http\Controllers\Dispatcher;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\DispatchedOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_assigned()
  {
    $orders = Order::whereDispatcherId(auth('dispatcher')->user()->id)->whereStatus('dispatched')->paginate(20);
    $response['status'] = 'success';
    $response['assigned_orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function delivered()
  {
    $orders = Order::whereDispatcherId(auth('dispatcher')->user()->id)->whereStatus('delivered')->paginate(20);
    $response['status'] = 'success';
    $response['delivered_orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  public function confirm(Request $request)
  {
    $this->validate($request, [
      'dispatcher_code' => 'required:alpha_num|size:6|exists:dispatchers',
    ]);
    $order = Order::whereDispatcherCode($request->dispatcher_code)->firstOrFail();
    $order->status = 'completed';
    $order->update();
    $order->user()->notify(new DispatchedOrder($order->user(), $order, $order->dispatcher_id = Auth('dispatcher')->user()));
    $response['status'] = 'success';
    $response['messages'] = 'Order #' . $order->code . ' has been Dispatched';
    return response()->json($response, Response::HTTP_OK);
  }
}
