<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Mail\OrderAlmostReady;
use App\Mail\OrderInKitchen;
use App\Mail\OrderPrepareCompleted;
use App\Models\Dispatcher;
use App\Models\Order;
use App\Models\User;
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
  public function index_open()
  {
    $orders = auth('chef')
      ->user()
      ->open_orders()
      ->whereDate('created_at', '<=', now())
      ->paginate(20);
    $response['status'] = 'success';
    $response['orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_in_kitchen()
  {
    $orders = auth('chef')
      ->user()
      ->in_kitchen_orders()
      ->paginate(20);
    $response['status'] = 'success';
    $response['orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_almost_ready()
  {
    $orders = auth('chef')->user()->almost_ready_orders()
      ->paginate(20);
    $response['status'] = 'success';
    $response['orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_prepared()
  {
    $orders = auth('chef')->user()->prepared_orders()->paginate(20);
    $response['status'] = 'success';
    $response['orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  public function mark_as_in_kitchen(Request $request)
  {
    $this->validate($request, [
      'order_code' => 'required|alpha_num|size:8|exists:orders,code',
    ]);
    $order = Order::whereDispatcherId(auth('chef')->user()->dispatcher->id)
      ->whereCode($request->order_code)
      ->whereStatus('confirmed')
      ->firstOrFail();
    $order->status = 'in_kitchen';
    $order->update();
    $order_user = User::whereId($order->user_id)->firstOrFail();
    Mail::to($order_user)->send(new OrderInKitchen($order_user, $order));
    $response['status'] = 'success';
    $response['messages'] = 'Order #' . $order->code . ' status set to: Now in Kitchen';
    return response()->json($response, Response::HTTP_OK);
  }

  public function mark_as_almost_ready(Request $request)
  {
    $this->validate($request, [
      'order_code' => 'required|alpha_num|size:8|exists:orders,code',
    ]);
    $order = Order::whereDispatcherId(auth('chef')->user()->dispatcher->id)
      ->whereCode($request->order_code)
      ->whereStatus('in_kitchen')
      ->firstOrFail();
    $order->status = 'almost_ready';
    $order->update();
    $order_user = User::whereId($order->user_id)->firstOrFail();
    Mail::to($order_user)->send(new OrderAlmostReady($order_user, $order));
    $response['status'] = 'success';
    $response['messages'] = 'Order #' . $order->code . ' status set to: In Kitchen 5 more minutes';
    return response()->json($response, Response::HTTP_OK);
  }

  public function mark_as_prepare_completed(Request $request)
  {
    $this->validate($request, [
      'order_code' => 'required|alpha_num|size:8|exists:orders,code',
    ]);
    $order = Order::whereDispatcherId(auth('chef')->user()->dispatcher->id)
      ->whereCode($request->order_code)
      ->whereIn('status', ['in_kitchen', 'almost_ready'])
      ->firstOrFail();
    $order->status = 'prepare_completed';
    $order->update();
    $order_user = User::whereId($order->user_id)->firstOrFail();
    Mail::to($order_user)->send(new OrderPrepareCompleted($order_user, $order));
    $response['status'] = 'success';
    $response['messages'] = 'Order #' . $order->code . ' status set to: Prepare Complete';
    return response()->json($response, Response::HTTP_OK);
  }

  public function get_order_details($order_code)
  {
    try {
      $order = Order::with(['user'])->whereCode($order_code)->whereDispatcherId(auth('chef')->user()->dispatcher_id)->firstOrFail();
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
