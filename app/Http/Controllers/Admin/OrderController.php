<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderAlmostReady;
use App\Mail\OrderCompleted;
use App\Mail\OrderConfirmed;
use App\Mail\OrderDispatched;
use App\Mail\OrderInKitchen;
use App\Mail\OrderPrepareCompleted;
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
  public function index_assigned($status)
  {
    $statuses = [];
    if (in_array($status, ['new', 'confirmed', 'dispatched', 'completed', 'in_kitchen', 'prepare_completed', 'almost_ready', 'future'])) {
      $statuses = [$status];
    } elseif ($status == 'cancelled') {
      $statuses = ['cancelled', 'cancelled_failed_payment', 'cancelled_system', 'cancelled_user'];
    } else {
      $statuses = ['new'];
    }
    if ($status == 'future') {
      $orders = Order::with(['user'])
        ->wherePlaceId(auth('admin')->user()->place_id)
        ->where('status', 'confirmed')
        ->whereDate('created_at', '>=', now()->addDay())
        ->paginate(20);
    } else {
      $orders = Order::with(['user'])
        ->wherePlaceId(auth('admin')->user()->place_id)
        ->whereIn('status', $statuses)
        ->whereDate('created_at', '<=', now())
        ->paginate(20);
    }
    $response['status'] = 'success';
    $response['orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_all($status)
  {
    $statuses = [];
    if (in_array($status, ['new', 'confirmed', 'dispatched', 'completed', 'in_kitchen', 'prepare_completed', 'almost_ready', 'future'])) {
      $statuses = [$status];
    } elseif ($status == 'cancelled') {
      $statuses = ['cancelled', 'cancelled_failed_payment', 'cancelled_system', 'cancelled_user'];
    } else {
      $statuses = ['new'];
    }
    if ($status == 'future') {
      $orders = Order::with(['user'])->where('status', 'confirmed')
        ->whereDate('created_at', '>=', now()->addDay())
        ->paginate(20);
    } else {
      $orders = Order::with(['user'])
        ->whereIn('status', $statuses)
        ->whereDate('created_at', '<=', now())
        ->paginate(20);
    }
    $response['status'] = 'success';
    $response['orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }


  public function change_status(Request $request)
  {
    $this->validate($request, [
      'order_id' => 'required|uuid|exists:orders,id',
      'new_status' => 'required|alpha_dash|in:confirmed,cancelled,dispatched,completed,in_kitchen,prepare_completed,almost_ready',
      'dispatch_type' => 'required_if:new_status,dispatched|in:pickup,door_delivery',
      'dispatcher_code' => 'required_if:dispatch_type,door_delivery|nullable|alpha_num|size:6|exists:dispatchers,code',
    ]);
    if ($request->new_status == 'completed') {
      $order = Order::with(['user', 'ordered_meals'])->whereId($request->order_id)->firstOrFail();
      $order->status = 'completed';
      $order->update();
      $order_user = User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderCompleted($order_user, $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been Completed';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'in_kitchen') {
      $order = Order::with(['user', 'ordered_meals'])->whereId($request->order_id)->firstOrFail();
      $order->status = 'in_kitchen';
      $order->update();
      $order_user = User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderInKitchen($order_user, $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' status set to: Now in Kitchen';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'prepare_completed') {
      $order = Order::with(['user', 'ordered_meals'])->whereStatus('almost_ready')->whereId($request->order_id)->firstOrFail();
      $order->status = 'prepare_completed';
      $order->update();
      $order_user = User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderPrepareCompleted($order_user, $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' status set to: Prepare Complete';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'almost_ready') {
      $order = Order::with(['user', 'ordered_meals'])->whereStatus('in_kitchen')->whereId($request->order_id)->firstOrFail();
      $order->status = 'almost_ready';
      $order->update();
      $order_user = User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderAlmostReady($order_user, $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' status set to: Almost Ready';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'cancelled') {
      $order = Order::with('ordered_meals')->whereId($request->order_id)->firstOrFail();
      $order->status = 'cancelled';
      $order->update();
      $order_user = User::whereId($order->user_id)->firstOrFail();
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
      $order->dispatcher_id = $dispatcher->id;
      $order->status = 'dispatched';
      $order->gen_dispatch_code();
      $order_user = User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderDispatched($order_user, $order, $dispatcher));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been Dispatched';
      return response()->json($response, Response::HTTP_OK);
    } elseif ($request->new_status == 'confirmed') {
      $order = Order::with('ordered_meals')->whereId($request->order_id)->firstOrFail();
      $order->status = 'confirmed';
      $order->update();
      $order_user = User::whereId($order->user_id)->firstOrFail();
      Mail::to($order_user)->send(new OrderConfirmed($order_user, $order));
      $response['status'] = 'success';
      $response['messages'] = 'Order #' . $order->code . ' has been Confirmed';
      return response()->json($response, Response::HTTP_OK);
    }
  }
}
