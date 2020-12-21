<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ExtraItem;
use App\Models\Meal;
use App\Models\Subcategory;
use App\Services\MealSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
// use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class MealController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $meals = Meal::paginate(20);
    $response['status'] = 'success';
    $response['meals'] = $meals;
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
      'name' => 'required|string|min:3|max:240',
      'price' => 'required|integer|digits_between:1,999999',
      'image' => 'required|image|mimes:png,jpg,jpeg,svg,gif|max:50000',
      'category' => 'required|alpha_dash|exists:categories,slug',
      'subcategory' => 'required|alpha_dash|exists:subcategories,slug',
      'measurement_quantity' => 'required|integer',
      'measurement_type' => 'required|in:piece,g,kg,cube',
      'description' => 'required|string|',
      'meal_extras' => 'sometimes|nullable|array|',
      'meal_extras.*.name' => 'required|string|min:3|max:240',
      'meal_extras.*.price' => 'required|integer|digits_between:1,999999',
      'meal_extras.*.measurement_quantity' => 'required|integer|',
      'meal_extras.*.measurement_type' => 'required|in:piece,g,kg,cube',
    ]);

    try {
      $cat = Category::whereSlug($request->category)->firstOrFail();
      $subcat = Subcategory::whereSlug($request->subcategory)->firstOrFail();

      $new_meal = new Meal();
      $new_meal->name = $request->name;
      $new_meal->price = $request->price;
      $new_meal->category_id = $cat->id;
      $new_meal->subcategory_id = $subcat->id;
      $new_meal->available = true;
      $new_meal->description = $request->description;
      $new_meal->measurement_type = $request->measurement_type;
      $new_meal->measurement_quantity = $request->measurement_quantity;

      //adding images
      if ($request->hasFile('image') && $request->file('image') != null) {
        $image = Image::make($request->file('image'))->encode('jpg', 1);
        // if (Auth()->user()->avatar != null) {
        //   if (File::exists("images/meals/" . Auth()->user()->avatar)) {
        //     File::delete("images/meals/" . Auth()->user()->avatar);
        //   }
        // }
        $img_name = sprintf("MEAL%s%s.jpg", md5($request->name . now()->format('y-m-d H:i:s.u')));
        $image->save(public_path("images/meals/") . $img_name, 70, 'jpg');
        $new_meal->image = $img_name;
      }
      $new_meal->saveOrFail();
      if ($request->has('meal_extras') && is_array($request->meal_extras) && count($request->meal_extras)) {
        $new_extras_items = $request->meal_extras;
        foreach ($new_extras_items as $meal_extra) {
          $new_extra_item = ExtraItem::firstOrCreate(
            ['name' => $meal_extra['name']],
            [
              'available' => true,
              'price' => $meal_extra['price'],
              'measurement_type' => $meal_extra['measurement_type'],
              'measurement_quantity' => $meal_extra['measurement_quantity'],
            ]
          );
          $new_meal->extra_items()->attach([$new_extra_item->id,]);
        }
      }
      $response['status'] = 'success';
      $response['message'] = $new_meal->name . ' has been added to the menu.';
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  Str  $meal_slug
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    $this->validate($request, [
      'meal' => 'required|alpha_dash|exists:meals,slug',
      'name' => 'sometimes|nullable|string|min:3|max:240',
      'price' => 'sometimes|nullable|integer|digits_between:1,999999',
      'image' => 'sometimes|nullable|image|mimes:png,jpg,jpeg,svg,gif|max:50000',
      'category' => 'sometimes|nullable|alpha_dash|exists:categories,slug',
      'subcategory' => 'sometimes|nullable|alpha_dash|exists:subcategories,slug',
      'measurement_quantity' => 'sometimes|nullable|integer',
      'measurement_type' => 'sometimes|nullable|in:piece,g,kg,cube',
      'description' => 'sometimes|nullable|string|',
      'meal_extras' => 'sometimes|nullable|array|',
      'meal_extras.*.name' => 'required|string|min:3|max:240',
      'meal_extras.*.price' => 'required|integer|digits_between:1,999999',
      'meal_extras.*.measurement_quantity' => 'required|integer|',
      'meal_extras.*.measurement_type' => 'required|in:piece,g,kg,cube',
    ]);

    try {
      $updateable_meal = Meal::whereSlug($request->meal)->firstOrFail();
      $attribs = [
        'name', 'price', 'measurement_quantity',
        'measurement_type', 'available', 'description',
        'category', 'subcategory',
      ];
      foreach ($attribs as $attrib) {
        if ($request->has($attrib) && $request->{$attrib} != (null || '')) {
          if ($attrib == 'category') {
            $cat = Category::whereSlug($request->category)->firstOrFail();
            $updateable_meal->{$attrib . '_id'} = $cat->id;
          } elseif ($attrib == 'subcategory') {
            $subcat = Subcategory::whereSlug($request->subcategory)->firstOrFail();
            $updateable_meal->{$attrib . '_id'} = $subcat->id;
          } else {
            $updateable_meal->{$attrib} = $request->{$attrib};
          }
        }
      }
      $updateable_meal->update();


      //adding images
      if ($request->hasFile('image') && $request->file('image') != null) {
        $image = Image::make($request->file('image'))->encode('jpg', 1);
        if ($updateable_meal->image != null) {
          if (File::exists(public_path("images/meals/") . $updateable_meal->image)) {
            File::delete(public_path("images/meals/") . $updateable_meal->image);
          }
        }
        $img_name = sprintf("MEAL%s%s.jpg", md5($request->name . now()->format('y-m-d H:i:s.u')));
        $image->save(public_path("images/meals/") . $img_name, 70, 'jpg');
        $updateable_meal->image = $img_name;
      }

      $updateable_meal->update();


      if ($request->has('meal_extras') && is_array($request->meal_extras) && count($request->meal_extras)) {

        return  $new_extras_items = $request->meal_extras;
        foreach ($new_extras_items as $meal_extra) {
          $new_extra_item = ExtraItem::firstOrCreate(
            ['name' => $meal_extra['name']],
            [
              'available' => true,
              'price' => $meal_extra['price'],
              'measurement_type' => $meal_extra['measurement_type'],
              'measurement_quantity' => $meal_extra['measurement_quantity'],
            ]
          );
          $updateable_meal->extra_items()->attach([$new_extra_item->id,]);
        }
      }
      $response['status'] = 'success';
      $response['message'] = $updateable_meal->name . ' has been Updated';
      return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * make a meal available.
   *
   * @param  Str  $meal_slug
   * @return \Illuminate\Http\Response
   */
  public function make_available($meal_slug)
  {
    $meal = Meal::whereSlug($meal_slug)->firstOrFail();
    $meal->make_available();
    $response['status'] = 'success';
    $response['message'] = $meal->name . ' has been marked as Available';
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * make a meal unavailable.
   *
   * @param  Str  $meal_slug
   * @return \Illuminate\Http\Response
   */
  public function make_unavailable($meal_slug)
  {
    $meal = Meal::whereSlug($meal_slug)->firstOrFail();
    $meal->make_unavailable();
    $response['status'] = 'success';
    $response['message'] = $meal->name . ' has been marked as Unavailable';
    return response()->json($response, Response::HTTP_OK);
  }



  /**
   * remove an extra item from a meal.
   *
   * @param  Str  $meal_slug
   * @param  Str  $extra_item_slug
   * @return \Illuminate\Http\Response
   */
  public function remove_single_extra_item($meal_slug, $extra_item_slug)
  {
    $meal = Meal::whereSlug($meal_slug)->firstOrFail();
    $extra_item = ExtraItem::whereSlug($extra_item_slug)->firstOrFail();
    $meal->remove_single_extra_item($extra_item->id);
    $response['status'] = 'success';
    $response['message'] = $extra_item->name . ' has been removed as an extra item from ' . $meal->name;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * remove an extra item from a meal.
   *
   * @param  Str  $meal_slug
   * @return \Illuminate\Http\Response
   */
  public function remove_all_extra_items($meal_slug)
  {
    $meal = Meal::whereSlug($meal_slug)->firstOrFail();
    $meal->remove_all_extra_items();
    $response['status'] = 'success';
    $response['message'] =  'All extra items from ' . $meal->name;
    return response()->json($response, Response::HTTP_OK);
  }
}
