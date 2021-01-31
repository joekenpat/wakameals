<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ChefController as AdminChefController;
use App\Http\Controllers\Admin\DispatcherController as AdminDispatcherController;
use App\Http\Controllers\Admin\MealController as AdminMealController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PlaceController as AdminPlaceController;
use App\Http\Controllers\Admin\SubcategoryController as AdminSubcategoryController;
use App\Http\Controllers\Admin\TableReservationController as AdminTableReservationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Chef\ChefController;
use App\Http\Controllers\Chef\OrderController as ChefOrderController;
use App\Http\Controllers\Dispatcher\ChefController as DispatcherChefController;
use App\Http\Controllers\Dispatcher\DispatchController;
use App\Http\Controllers\Dispatcher\OrderController as DispatcherOrderController;
use App\Http\Controllers\User\MealController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\PlaceController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\DispatcherController;
use App\Http\Controllers\User\TableReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});


//guest routes
Route::group([], function () {

  //user auth route
  Route::group(['prefix' => 'auth'], function () {
    //user registration route
    Route::post('register/default', [UserController::class, 'default_register']);
    //user login route
    Route::post('login/default', [UserController::class, 'default_login']);
  });
  Route::group(['prefix' => 'profile', 'middleware' => ['auth:user']], function () {
    //user profile route
    Route::get('details', [UserController::class, 'show']);
    Route::post('update', [UserController::class, 'update']);
    //user password update
    Route::post('password/update', [UserController::class, 'update_password']);
  });

  //guest pickup  route
  Route::get('avail_pickup/{place_slug}/list', [DispatcherController::class, 'index'])->where('place_slug', '[a-z0-9-]+');
  //guest meal route
  Route::get('meal/list', [MealController::class, 'index']);

  //guest cart route
  Route::post('cart/new', [CartController::class, 'store']);
  Route::post('cart/remove', [CartController::class, 'destroy'])->middleware('auth:user');
  Route::post('cart/sync', [CartController::class, 'sync_cart']);

  //user order route
  Route::group(['prefix' => 'order', 'middleware' => ['auth:user']], function () {
    Route::get('list/open', [OrderController::class, 'index_open']);
    Route::get('list/closed', [OrderController::class, 'index_closed']);
    Route::get('verify_payment', [OrderController::class, 'verify_paystack_transaction']);
    Route::post('new', [OrderController::class, 'store']);
  });

  //user table reservation route
  Route::group(['prefix' => 'reservation', 'middleware' => ['auth:user']],  function () {
    Route::post('new', [TableReservationController::class, 'store']);
    Route::get('list/approved', [TableReservationController::class, 'index_approved']);
    Route::get('list/closed', [TableReservationController::class, 'index_closed']);
    Route::get('list/pending', [TableReservationController::class, 'index_pending']);
    Route::get('list/cancelled', [TableReservationController::class, 'index_cancelled']);
    Route::get('cancel/{reservation_code}', [TableReservationController::class, 'cancel'])->whereAlphaNumeric(['reservation_code']);
    Route::get('delete/{reservation_code}', [TableReservationController::class, 'delete'])->whereAlphaNumeric(['reservation_code']);
  });
  //guest place route
  Route::get('place/list', [PlaceController::class, 'index']);
});

//dispatcher routes
Route::group(['prefix' => 'dispatcher'], function () {

  //user auth route
  Route::group(['prefix' => 'auth'], function () {
    //user registration route
    Route::post('register/default', [DispatchController::class, 'default_register']);
    //user login route
    Route::post('login/default', [DispatchController::class, 'default_login']);
  });
  Route::group(['prefix' => 'profile', 'middleware' => ['auth:dispatcher']], function () {
    //user profile route
    Route::get('details', [DispatchController::class, 'show']);
    Route::post('update', [DispatchController::class, 'update']);
    Route::post('password/update', [DispatchController::class, 'update_password']);
  });

  //dispatcher order route
  Route::group(['prefix' => 'order', 'middleware' => ['auth:dispatcher']], function () {
    Route::get('list/assigned', [DispatcherOrderController::class, 'index_assigned']);
    Route::get('list/delivered', [DispatcherOrderController::class, 'index_delivered']);
    Route::post('confirm', [DispatcherOrderController::class, 'confirm']);
    Route::get('get_order_details/{dispatch_code}', [DispatcherOrderController::class, 'get_order_details'])->whereAlphaNumeric(['dispatch_code']);
  });

  //dispatcher chef route
  Route::group(['prefix' => 'chef'], function () {
    Route::get('list/active', [DispatcherChefController::class, 'index_active']);
    Route::get('list/blocked', [DispatcherChefController::class, 'index_blocked']);
    Route::get('list/pending', [DispatcherChefController::class, 'index_pending']);
    Route::get('block/{chef_id}', [DispatcherChefController::class, 'block'])->whereAlphaNumeric(['chef_id']);
    Route::get('activate/{chef_id}', [DispatcherChefController::class, 'unblock'])->whereAlphaNumeric(['chef_id']);
    Route::get('delete/{chef_id}', [DispatcherChefController::class, 'delete'])->whereAlphaNumeric(['chef_id']);
  });
});

//chef routes
Route::group(['prefix' => 'chef'], function () {

  //chef auth route
  Route::group(['prefix' => 'auth'], function () {
    //chef registration route
    Route::post('register/default', [ChefController::class, 'default_register']);
    //chef login route
    Route::post('login/default', [ChefController::class, 'default_login']);
  });
  Route::group(['prefix' => 'profile', 'middleware' => ['auth:chef']], function () {
    //chef profile route
    Route::get('details', [ChefController::class, 'show']);
    Route::post('update', [ChefController::class, 'update']);
    Route::post('password/update', [ChefController::class, 'update_password']);
  });

  //chef order route
  Route::group(['prefix' => 'order', 'middleware' => ['auth:chef']], function () {
    Route::get('list/open', [ChefOrderController::class, 'index_open']);
    Route::get('list/processing', [ChefOrderController::class, 'index_processing']);
    Route::get('list/prepared', [ChefOrderController::class, 'index_prepared']);
    Route::post('mark-as/in_kitchen', [ChefOrderController::class, 'mark_as_in_kitchen']);
    Route::post('mark-as/almost_ready', [ChefOrderController::class, 'mark_as_almost_ready']);
    Route::post('mark-as/prepare_completed', [ChefOrderController::class, 'mark_as_prepare_completed']);
    Route::get('get_order_details/{order_code}', [ChefOrderController::class, 'get_order_details'])->whereAlphaNumeric(['order_code']);
  });
});

//admin route
Route::group(['prefix' => 'admin'], function () {

  //user auth route
  Route::group(['prefix' => 'auth'], function () {
    //user registration route
    // Route::post('register/default', [UserController::class, 'default_register']);
    //user lgin route
    Route::post('login/default', [AdminController::class, 'default_login']);
  });

  //admin order route
  Route::group(['prefix' => 'order', 'middleware' => ['auth:admin']], function () {
    Route::get('list/assigned/{status}', [AdminOrderController::class, 'index_assigned'])->where(['status' => 'new|confirmed|cancelled|dispatched|completed|in_kitchen|prepare_completed|almost_ready']);
    Route::get('list/all/{status}', [AdminOrderController::class, 'index_all'])->where(['status' => 'new|confirmed|cancelled|dispatched|completed|in_kitchen|prepare_completed|almost_ready']);
    Route::post('set_status', [AdminOrderController::class, 'change_status']);
  });


  //admin dispatcher route
  Route::group(['prefix' => 'dispatcher', 'middleware' => ['auth:admin']], function () {
    Route::get('list/active', [AdminDispatcherController::class, 'index_active']);
    Route::get('list/pending', [AdminDispatcherController::class, 'index_pending']);
    Route::get('list/blocked', [AdminDispatcherController::class, 'index_blocked']);
    Route::get('block/{dispatcher_code}', [AdminDispatcherController::class, 'block'])->whereAlphaNumeric(['dispatcher_code']);
    Route::get('activate/{dispatcher_code}', [AdminDispatcherController::class, 'activate'])->whereAlphaNumeric(['dispatcher_code']);
    Route::get('delete/{dispatcher_code}', [AdminDispatcherController::class, 'delete'])->whereAlphaNumeric(['dispatcher_code']);
  });

  //admin table reservation route
  Route::group(['prefix' => 'reservation', 'middleware' => ['auth:admin']], function () {
    Route::get('list/approved', [AdminTableReservationController::class, 'index_approved']);
    Route::get('list/closed', [AdminTableReservationController::class, 'index_closed']);
    Route::get('list/pending', [AdminTableReservationController::class, 'index_pending']);
    Route::get('list/cancelled', [AdminTableReservationController::class, 'index_cancelled']);
    Route::get('approve/{reservation_code}', [AdminTableReservationController::class, 'approve'])->whereAlphaNumeric(['reservation_code']);
    Route::get('cancel/{reservation_code}', [AdminTableReservationController::class, 'cancel'])->whereAlphaNumeric(['reservation_code']);
    Route::get('delete/{reservation_code}', [AdminTableReservationController::class, 'delete'])->whereAlphaNumeric(['reservation_code']);
  });

  //admin chef route
  Route::group(['prefix' => 'chef', 'middleware' => ['auth:admin']], function () {
    Route::get('list/active', [AdminChefController::class, 'index_active']);
    Route::get('list/blocked', [AdminChefController::class, 'index_blocked']);
    Route::get('list/pending', [AdminChefController::class, 'index_pending']);
    Route::get('block/{chef_id}', [AdminChefController::class, 'block'])->whereUuid(['chef_id']);
    Route::get('activate/{chef_id}', [AdminChefController::class, 'unblock'])->whereUuid(['chef_id']);
    Route::get('delete/{chef_id}', [AdminDispatcherController::class, 'delete'])->whereUuid(['chef_id']);
  });


  //admin user route
  Route::group(['prefix' => 'user', 'middleware' => ['auth:admin']], function () {
    Route::get('list/active', [AdminUserController::class, 'index_active']);
    Route::get('list/blocked', [AdminUserController::class, 'index_blocked']);
    Route::get('block/{user_id}', [AdminUserController::class, 'block'])->whereUuid(['user_id']);
    Route::get('activate/{user_id}', [AdminUserController::class, 'activate'])->whereUuid(['user_id']);
  });

  //admin meal route
  Route::group(['prefix' => 'meal', 'middleware' => ['auth:admin']], function () {
    Route::get('list/available', [AdminMealController::class, 'index_available']);
    Route::get('list/unavailable', [AdminMealController::class, 'index_unavailable']);
    Route::get('make_available/{meal_slug}', [AdminMealController::class, 'make_available'])->where('meal_slug', '[a-z0-9-]+');
    Route::get('make_unavailable/{meal_slug}', [AdminMealController::class, 'make_unavailable'])->where('meal_slug', '[a-z0-9-]+');
    Route::get('remove_extra_item/{meal_slug}/{extra_item_slug}', [AdminMealController::class, 'remove_single_extra_item'])->where(['meal_slug' => '[a-z0-9-]+', 'extra_item_slug' => '[a-z0-9-]+']);
    Route::get('remove_extra_item/{meal_slug}', [AdminMealController::class, 'remove_all_extra_item'])->where(['meal_slug' => '[a-z0-9-]+']);
    Route::get('make_unavailable/{meal_slug}', [AdminMealController::class, 'make_unavailable'])->where(['meal_slug' => '[a-z0-9-]+', 'extra_item_slug' => '[a-z0-9-]+']);
    Route::post('new', [AdminMealController::class, 'store']);
    Route::post('update', [AdminMealController::class, 'update']);
  });


  //admin category route
  Route::group(['prefix' => 'category', 'middleware' => ['auth:admin']], function () {
    Route::get('list', [AdminCategoryController::class, 'index']);
    Route::post('new', [AdminCategoryController::class, 'store']);
    Route::post('update/{category_slug}', [AdminCategoryController::class, 'update'])->where('category_slug', '[a-z0-9-]+');
  });

  //admin subcategory route
  Route::group(['prefix' => 'subcategory', 'middleware' => ['auth:admin']], function () {
    Route::get('list_all', [AdminSubcategoryController::class, 'index']);
    Route::get('list/{category_slug}', [AdminSubcategoryController::class, 'index_cat'])->where('category_slug', '[a-z0-9-]+');
    Route::post('new', [AdminSubcategoryController::class, 'store']);
    Route::post('update/{subcategory_slug}', [AdminSubcategoryController::class, 'update'])->where('subcategory_slug', '[a-z0-9-]+');
  });

  //admin place route
  Route::group(['prefix' => 'place', 'middleware' => ['auth:admin']], function () {
    Route::post('new', [AdminPlaceController::class, 'store']);
    Route::post('update/{place_slug}', [AdminPlaceController::class, 'update'])->where('place_slug', '[a-z0-9-]+');
    Route::get('list/enabled', [AdminPlaceController::class, 'index_enabled']);
    Route::get('list/disabled', [AdminPlaceController::class, 'index_disabled']);
    Route::get('enable/{place_slug}', [AdminPlaceController::class, 'enable'])->where('place_slug', '[a-z0-9-]+');
    Route::get('disable/{place_slug}', [AdminPlaceController::class, 'disable'])->where('place_slug', '[a-z0-9-]+');
  });
});
