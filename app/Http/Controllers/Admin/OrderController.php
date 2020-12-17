<?php

namespace App\Http\Controllers;

use App\Models\Dispatcher;
use App\Models\Order;
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
      'dispatcher_code' => 'required_if:new_status,dispatched|alpha_num|size:8|exists:dispatchers,code'
    ]);
    if (in_array($request->new_status, ['completed', 'confirmed', 'cancelled'])) {
      $order = Order::find($request->order_id);
      $order->status = $request->new_status;
    } else {
      $dispatcher = Dispatcher::whereCode($request->dispatcher_code)->firstOrFail();
      $order = Order::find($request->order_id);
      
    }
  }
}
