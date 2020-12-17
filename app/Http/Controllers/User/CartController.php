<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ExtraItem;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
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
      'id' => 'required|uuid',
      'name' => 'required|regex:/[A-Za-z0-9_ -]+/',
      'meal_id' => 'required|uuid|exists:meals,id',
      'special_instruction' => 'sometimes|nullable|string',
      'meal_extras' => 'sometimes|array|min:0',
      'meal_extras.*.id' => 'required|exists:extra_items,id',
      'meal_extras.*.quantity' => 'required|numeric|min:1|max:100',
    ]);
    try {
      $cart_item = new Cart($request->only(
        [
          'name',
          'user_id',
          'meal_id',
          'special_instruction',
          'meal_extras',
        ]
      ));
      if (Auth('user')->check()) {
        $cart_item->save();
      }
      $cart_item->meal = Meal::find($cart_item->meal_id)->makeHidden(['subcategory', 'category', 'extra_items', 'subcategory_id', 'category_id',]);
      $cart_item->meal_extra_items = $this->meal_extra_items($cart_item);
      $cart_item->sub_total = $this->sub_total($cart_item);
      $response['status'] = 'success';
      $response['message'] = 'Item has been added to cart';
      $response['cart_item'] = $cart_item;
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * sync client cart item with server.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function sync_cart(Request $request)
  {
    $this->validate($request, [
      'items' => 'required|array',
      'items.*.id' => 'required|uuid',
      'items.*.name' => 'required|regex:/[A-Za-z0-9_ -]+/',
      'items.*.meal_id' => 'required|uuid|exists:meals,id',
      'items.*.special_instruction' => 'sometimes|nullable|string',
      'items.*.meal_extras' => 'sometimes|array|min:0',
      'items.*.meal_extras.*.id' => 'required|exists:extra_items,id',
      'items.*.meal_extras.*.quantity' => 'required|numeric|min:1|max:100',
    ]);
    try {
      $item_ids = [];
      $processed_cart_items = [];
      $processed_cart_items['items'] = [];
      $cart_total = 0;
      foreach ($request->input('items') as $item) {
        $item_ids[] = $item['id'];
        $cart_item = new Cart(
          [
            'name' => $item['name'],
            'user_id' => null,
            'meal_id' => $item['meal_id'],
            'special_instruction' => $item['special_instruction'],
            'meal_extras' => $item['meal_extras'],
          ]
        );
        if (Auth('user')->check()) {
          $cart_item->firstOrCreate();
        };
        $cart_item->meal = Meal::find($cart_item->meal_id)->makeHidden(['subcategory', 'category', 'extra_items', 'subcategory_id', 'category_id',]);
        $cart_item->meal_extra_items = $this->meal_extra_items($cart_item);
        $cart_item->sub_total = $this->sub_total($cart_item);
        $cart_total += $cart_item->sub_total;
        $processed_cart_items['items'][] = $cart_item;
      }
      $processed_cart_items['total'] = $cart_total;
      $processed_cart_items['item_count'] = count($processed_cart_items['items']);
      $response['status'] = 'success';
      $response['message'] = 'Cart Updated';
      $response['cart'] = $processed_cart_items;
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function meal_extra_items(Cart $cart)
  {
    $meal_extrx = $cart->meal_extras;
    $padded_meal_extras = [];
    foreach ($meal_extrx as $me) {
      $padded_meal_extras[] = $this->meal_extra_item_with_cost($me['id'], $me['quantity']);
    }
    return $padded_meal_extras;
  }


  function meal_extra_item_with_cost($meal_extra_item_id, $selected_quantity)
  {
    $ext = ExtraItem::whereId($meal_extra_item_id)->firstOrFail();
    $ext->selected_quantity = $selected_quantity;
    $ext->sub_cost = $ext->price * $selected_quantity;
    return $ext;
  }

  public function sub_total(Cart $cart)
  {
    $meal_cost = $cart->meal->price ?: 0;
    $extra_items_cost = 0;
    foreach ($cart->meal_extra_items as $meal_item) {
      $extra_items_cost += $meal_item->sub_cost ?: 0;
    }
    return $meal_cost + $extra_items_cost;
  }
}
