<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderedMeal;
use App\Models\OrderedMealExtraItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $orders = Order::whereUserId(Auth('user')->user()->id)->paginate(20);
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
      'town' => 'required||integer|exists:towns,id',
      'state' => 'required||integer|exists:states,id',
      'lga' => 'required||integer|exists:lgas,id',
      'address' => 'required|string|min:5|max:255',
      'recurring.dates.*' => 'sometimes|before_or_equal:7 days|after_or_equal:today',
      'recurring.times.*' => 'sometimes|before_or_equal:' . now()->addMinutes(45) . '|after_or_equal:today 5:15PM',
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

    try {
      $new_order = new Order();
      $new_order->state_id = $request->state;
      $new_order->lga_id = $request->lga;
      $new_order->town_id = $request->town;
      $new_order->address = $request->address;
      $new_order->status = 'created';
      $new_order->user_id = Auth('user')->user()->id;
      $new_order->saveOrFail();

      if ($request->has('meals') && is_array($request->meals) && count($request->meals)) {
        $ordered_meals = $request->meals;
        foreach ($ordered_meals as $meal_item) {
          $new_ordered_meal = new OrderedMeal();
          $new_ordered_meal->name = $meal_item->name;
          $new_ordered_meal->meal_id = $meal_item->meal_id;
          $new_ordered_meal->special_instruction = $meal_item->special_instruction;
          $new_ordered_meal->status = 'created';
          $new_ordered_meal->order_id = $new_order->id;
          $new_order->saveOrFail();

          if ($request->has('meals.extra_items') && is_array($request->meals['extra_items']) && count($request->meals['extra_items'])) {
            $ordered_meals_extra_items = $request->meals['extra_items'];
            foreach ($ordered_meals_extra_items as $meals_extra_item) {
              $new_ordered_meals_extra_item = new OrderedMealExtraItem();
              $new_ordered_meals_extra_item->meal_extra_item_id = $meals_extra_item->extra_item;
              $new_ordered_meals_extra_item->ordered_meal_id = $meals_extra_item->$new_ordered_meal->id;
              $new_ordered_meals_extra_item->quantity = $meals_extra_item->quantity;
              $new_ordered_meals_extra_item->status = 'created';
              $new_ordered_meals_extra_item->order_id = $new_order->id;
              $new_order->saveOrFail();
            }
          }
        }
      }


      $response['status'] = 'success';
      $response['message'] = 'Order Has Been to sent to the kitchen';
      $response['order'] = $new_order;
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}