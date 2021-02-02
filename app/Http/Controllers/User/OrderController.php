<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderReceived;
use App\Mail\OrderPaymentCancelled;
use App\Mail\OrderRecieved;
use App\Models\Dispatcher;
use App\Models\Order;
use App\Models\OrderedMeal;
use App\Models\OrderedMealExtraItem;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
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
    $orders = Order::whereUserId(Auth('user')->user()->id)->whereIn('status', ['dispatched', 'created', 'pending', 'new'])->paginate(20);
    $response['status'] = 'success';
    $response['orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index_closed()
  {
    $orders = Order::whereUserId(Auth('user')->user()->id)->whereIn('status', ['completed', 'cancelled_user', 'cancelled_system', 'cancelled_failed_payment'])->paginate(20);
    $response['status'] = 'success';
    $response['orders'] = $orders;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->validate($request, [
      'delivery_type' => 'required|in:door_delivery,pickup',
      'pickup_code' => 'required_if:delivery_type,pickup|nullable|exists:dispatchers,code',
      'place' => 'required_if:delivery_type,door_delivery|integer|exists:places,id',
      'address' => 'required_if:delivery_type,door_delivery|nullable|string|min:5|max:255',
      'recurring' => 'required|boolean',
      'recurring_dates' => 'exclude_if:recurring,false|array|min:1',
      'recurring_times' => 'exclude_if:recurring,false|array|min:1',
      'recurring_dates.*' => 'date_format:Y-m-d|before_or_equal:7 days|after_or_equal:tomorrow',
      'recurring_times.*' => 'date_format:H:i|before_or_equal:17:15|after_or_equal:7:00',
      'meals' => 'required|array|min:0',
      'meals.*.name' => 'required|regex:/[A-Za-z0-9_ -]+/',
      'meals.*.meal_id' => 'required|exists:meals,id',
      'meals.*.special_instruction' => 'sometimes|nullable|string',
      'meals.*.extra_items' => 'sometimes|array|min:0',
      'meals.*.extra_items.*.id' => 'required|exists:meal_extra_items,id',
      'meals.*.extra_items.*.quantity' => 'required|numeric|min:1|max:100',
    ], [
      'meals.*.meal.exists' => ':input is an invalid Meal identity',
    ]);
    $order_codes = [];

    try {

      $new_order = new Order();
      if ($request->delivery_type == 'pickup') {
        $dispatcher = Dispatcher::whereCode($request->pickup_code)->firstOrFail();
        $new_order->place_id = $dispatcher->place_id;
        $new_order->address = $dispatcher->address;
        $new_order->dispatcher_id = $dispatcher->id;
      } else {
        $new_order->place_id = $request->place;
        $new_order->address = $request->address;
      }
      $new_order->delivery_type = $request->delivery_type;
      $new_order->status = 'created';
      $new_order->type = 'one_time';
      $new_order->user_id = Auth('user')->user()->id;
      $new_order->save();

      if ($request->has('meals') && is_array($request->meals) && count($request->meals)) {
        $ordered_meals = $request->meals;
        foreach ($ordered_meals as $meal_item) {
          $new_ordered_meal = new OrderedMeal();
          $new_ordered_meal->name = $meal_item['name'];
          $new_ordered_meal->meal_id = $meal_item['meal_id'];
          $new_ordered_meal->special_instruction = $meal_item['special_instruction'];
          $new_ordered_meal->status = 'created';
          $new_ordered_meal->order_id = $new_order->id;
          $new_ordered_meal->save();

          if (is_array($meal_item['meal_extras']) && count($meal_item['meal_extras'])) {
            $ordered_meals_extra_items = $meal_item['meal_extras'];
            foreach ($ordered_meals_extra_items as $meals_extra_item) {
              $new_ordered_meals_extra_item = new OrderedMealExtraItem();
              $new_ordered_meals_extra_item->meal_extra_item_id = $meals_extra_item['id'];
              $new_ordered_meals_extra_item->ordered_meal_id = $new_ordered_meal->id;
              $new_ordered_meals_extra_item->quantity = $meals_extra_item['quantity'];
              $new_ordered_meals_extra_item->status = 'created';
              $new_ordered_meals_extra_item->save();
            }
          }
        }
      }
      $order_codes[] = $new_order->code;

      if ($request->recurring) {
        if ($request->has('recurring_dates') && is_array($request->recurring_dates) && count($request->recurring_dates)) {
          if ($request->has('recurring_times') && is_array($request->recurring_times) && count($request->recurring_times)) {
            foreach ($request->recurring_dates as $date) {
              foreach ($request->recurring_times as $time) {
                $new_order = new Order();
                if ($request->delivery_type == 'pickup') {
                  $dispatcher = Dispatcher::whereCode($request->pickup_code)->firstOrFail();
                  $new_order->place_id = $dispatcher->place_id;
                  $new_order->address = $dispatcher->address;
                  $new_order->dispatcher_id = $dispatcher->id;
                } else {
                  $new_order->place_id = $request->place;
                  $new_order->address = $request->address;
                }
                $new_order->delivery_type = $request->delivery_type;
                $new_order->type = 'recurring';
                $new_order->status = 'new';
                $new_order->user_id = Auth('user')->user()->id;
                $new_order->created_at = Carbon::parse("{$date}")->startOfDay()->setTimeFrom(Carbon::parse("{$time}"));
                $new_order->updated_at = $new_order->created_at;
                $new_order->save();

                if ($request->has('meals') && is_array($request->meals) && count($request->meals)) {
                  $ordered_meals = $request->meals;
                  foreach ($ordered_meals as $meal_item) {
                    $new_ordered_meal = new OrderedMeal();
                    $new_ordered_meal->name = $meal_item['name'];
                    $new_ordered_meal->meal_id = $meal_item['meal_id'];
                    $new_ordered_meal->special_instruction = $meal_item['special_instruction'];
                    $new_ordered_meal->status = 'created';
                    $new_ordered_meal->order_id = $new_order->id;
                    $new_ordered_meal->created_at = Carbon::parse("{$date}")->startOfDay()->setTimeFrom(Carbon::parse("{$time}"));
                    $new_ordered_meal->updated_at = $new_ordered_meal->created_at;
                    $new_ordered_meal->save();

                    if (is_array($meal_item['meal_extras']) && count($meal_item['meal_extras'])) {
                      $ordered_meals_extra_items = $meal_item['meal_extras'];
                      foreach ($ordered_meals_extra_items as $meals_extra_item) {
                        $new_ordered_meals_extra_item = new OrderedMealExtraItem();
                        $new_ordered_meals_extra_item->meal_extra_item_id = $meals_extra_item['id'];
                        $new_ordered_meals_extra_item->ordered_meal_id = $new_ordered_meal->id;
                        $new_ordered_meals_extra_item->quantity = $meals_extra_item['quantity'];
                        $new_ordered_meals_extra_item->status = 'created';
                        $new_ordered_meals_extra_item->created_at = Carbon::parse("{$date}")->startOfDay()->setTimeFrom(Carbon::parse("{$time}"));
                        $new_ordered_meals_extra_item->updated_at = $new_ordered_meals_extra_item->created_at;
                        $new_ordered_meals_extra_item->save();
                      }
                    }
                  }
                }
                $order_codes[] = $new_order->code;
              }
            }
          }
        }
      }

      $response['status'] = 'success';
      $response['message'] = 'Order Created';
      $response['order_codes'] = $order_codes;
      $response['order_total'] = $this->get_total_amount($order_codes);
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage() . " File :" . $e->getFile() . " Line: " . $e->getLine();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function verify_paystack_transaction(Request $request)
  {
    try {
      $paystack_client = Http::withToken(config('paystack.secretKey'))->get("https://api.paystack.co/transaction/verify/" . $request->query('trxref'));
      return $payment_details = $paystack_client->json();
      if ($payment_details['data']['status'] === "success") {
        $order_user = User::whereEmail($payment_details['data']['metadata']['email'])->firstOrFail();
        $orders = Order::whereCode($payment_details['data']['metadata']['order_codes'])
          ->get();
        foreach ($orders as $order) {
          if (($payment_details['data']['amount'] / 100) == $order->total) {
            $transaction =  new Transaction([
              'status' => 'completed',
              'total_amount' => ($payment_details['data']['amount'] / 100),
              'user_id' => $order_user->id,
              'order_id' => $order->id,
              'gateway' => 'paystack',
              'reference' => $payment_details['data']['reference'],
            ]);
            $transaction->save();
            $order->status = 'new';
            $order->update();
          }
          Mail::to($order_user)->send(new OrderRecieved($order_user, $order));
          foreach (['wdcebenezer@gmail.com', 'joekenpat@gmail.com'] as $recipient) {
            Mail::to($recipient)->send(new NewOrderReceived($order));
          }
        }
        $response['message'] = 'Order Payment Successfull';
      } else {
        if ($payment_details['data']['status'] === "failed") {
          $order_user = User::whereEmail($payment_details['data']['metadata']['email'])->firstOrFail();
          $orders = Order::whereCode($payment_details['data']['metadata']['order_codes'])
            ->get();
          foreach ($orders as $order) {
            $transaction =  new Transaction([
              'status' => 'failed',
              'total_amount' => ($payment_details['data']['amount'] / 100),
              'user_id' => $order_user->id,
              'order_id' => $order->id,
              'gateway' => 'paystack',
              'reference' => $payment_details['data']['reference'],
            ]);
            $transaction->save();
            $order->status = 'cancelled_failed_payment';
            $order->update();
            Mail::to($order_user)->send(new OrderPaymentCancelled($order_user, $order));
          }
          $response['message'] = 'Order Payment Failed';
        }
      }
      $response['status'] = 'success';
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }


  public function get_total_amount($order_codes)
  {
    $total = 0;
    foreach ($order_codes as $code) {
      if (Order::whereCode($code)->exists()) {
        $order = Order::whereCode($code)->firstOrFail();
        $total += $order->total;
      }
    }
    return $total;
  }
}
