<?php

namespace App\Http\Controllers\Dispatcher;

use App\Http\Controllers\Controller;
use App\Mail\OrderCompleted;
use App\Models\Order;
use App\Notifications\DispatchedOrder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
  public function index_assigned()
  {
    $orders = Order::with(['user'])->whereDispatcherId(auth('dispatcher')->user()->id)->whereStatus('dispatched')->paginate(20);
    $response['status'] = 'success';
    $response['assigned_orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_delivered()
  {
    $orders = Order::with(['user'])->whereDispatcherId(auth('dispatcher')->user()->id)->whereStatus('delivered')->paginate(20);
    $response['status'] = 'success';
    $response['delivered_orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  public function confirm(Request $request)
  {
    $this->validate($request, [
      'dispatch_code' => 'required:alpha_num|size:6|exists:orders',
    ]);
    $order = Order::whereDispatcherId(auth('dispatcher')->user()->id)->whereDispatchCode($request->dispatcher_code)->firstOrFail();
    $order->status = 'completed';
    $order->update();
    Mail::to($order->user())->send(new OrderCompleted($order->user(), $order));
    $response['status'] = 'success';
    $response['messages'] = 'Order #' . $order->code . ' has been Dispatched';
    return response()->json($response, Response::HTTP_OK);
  }

  public function get_order_details($dispatch_code)
  {
    try {
      $order = Order::with(['user'])->whereDispatcherId(auth('dispatcher')->user()->id)->whereDispatchCode($dispatch_code)->firstOrFail();
      $response['status'] = 'success';
      $response['order'] = $order;
      return response()->json($response, Response::HTTP_OK);
    } catch (ModelNotFoundException $mnf) {
      $response['status'] = 'error';
      $response['message'] = $mnf->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    } catch (\Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
  }
  }
}
