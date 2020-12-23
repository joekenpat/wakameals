<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\OrderSystemCancelled;
use App\Mail\OrderUserCancelled;
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
      'town' => 'required_if:delivery_type,door_delivery|integer|exists:towns,id',
      'state' => 'required_if:delivery_type,door_delivery|integer|exists:states,id',
      'lga' => 'required_if:delivery_type,door_delivery|integer|exists:lgas,id',
      'address' => 'required_if:delivery_type,door_delivery|string|min:5|max:255',
      'recurring_dates.*' => 'sometimes|before_or_equal:7 days|after_or_equal:today',
      'recurring_times.*' => 'sometimes|before_or_equal:' . now()->addMinutes(45) . '|after_or_equal:today 5:15PM',
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
    $orders = [];
    $order_ids = [];

    try {
      if ($request->has('recurring_dates') && is_array($request->recurring_dates) && count($request->recurring_dates)) {
        if ($request->has('recurring_times') && is_array($request->recurring_times) && count($request->recurring_times)) {
          foreach ($request->recurring_dates as $date) {
            foreach ($request->recurring_times as $time) {
              $new_order = new Order();
              if ($request->delivery_type == 'pickup') {
                $dispatcher = Dispatcher::whereCode($request->pickup_code)->firstOrFail();
                $new_order->state_id = $dispatcher->state_id;
                $new_order->lga_id = $dispatcher->lga_id;
                $new_order->town_id = $dispatcher->town_id;
                $new_order->address = $dispatcher->address;
                $new_order->dispatcher_id = $dispatcher->id;
              } else {
                $new_order->state_id = $request->state;
                $new_order->lga_id = $request->lga;
                $new_order->town_id = $request->town;
                $new_order->address = $request->address;
              }
              $new_order->delivery_type = $request->delivery_type;
              $new_order->status = 'created';
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
              $orders[] = $new_order;
              $order_ids[] = $new_order->id;
            }
          }
        }
      } else {
        $new_order = new Order();
        if ($request->delivery_type == 'pickup') {
          $dispatcher = Dispatcher::whereCode($request->pickup_code)->firstOrFail();
          $new_order->state_id = $dispatcher->state_id;
          $new_order->lga_id = $dispatcher->lga_id;
          $new_order->town_id = $dispatcher->town_id;
          $new_order->address = $dispatcher->address;
          $new_order->dispatcher_id = $dispatcher->id;
        } else {
          $new_order->state_id = $request->state;
          $new_order->lga_id = $request->lga;
          $new_order->town_id = $request->town;
          $new_order->address = $request->address;
        }
        $new_order->delivery_type = $request->delivery_type;
        $new_order->status = 'created';
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
        $orders[] = $new_order->refresh();
        $order_ids[] = $new_order->id;
      }


      $response['status'] = 'success';
      $response['message'] = 'Order Created';
      $response['orders'] = $orders;
      $response['order_ids'] = $order_ids;
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage() . " File :" . $e->getFile() . " Line: " . $e->getLine();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function verify_paystack_transaction(Request $request)
  {
    $paystack_client = Http::withToken(config('paystack.secretKey'))->get("https://api.paystack.co/transaction/verify/" . $request->query('trxref'));
    $payment_details = $paystack_client->json();
    if ($payment_details['data']['status'] === "success") {
      $order_user = User::whereEmail($payment_details['data']['metadata']['email']);
      $order = Order::select('id', 'title', 'slug', 'plan', 'plan_id')
        ->whereCode($payment_details['data']['metadata']['order_code'])
        ->firstOrFail();
      $transaction =  new Transaction([
        'status' => 'created',
        'total_amount' => 0,
        'user_id' => $order_user->id,
        'gateway' => 'paystack',
        'reference' => $payment_details['data']['reference'],
      ]);
      $transaction->status = 'completed';
      $transaction->save();

      $order->transactions()->save($transaction);
      $order->update();
    }
    $response['status'] = 'success';
    $response['message'] = 'Product Updated';
    return response()->json($response, Response::HTTP_OK);
  }

  public function test_order_mail($order_code)
  {
    $order = Order::with('ordered_meals')->whereCode($order_code)->firstOrFail();
    $user = User::whereId(auth('user')->user()->id)->firstOrFail();
    // return $order;
    return new OrderSystemCancelled($user, $order, $dispatcher);
  }
}
