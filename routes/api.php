<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DispatcherController as AdminDispatcherController;
use App\Http\Controllers\Admin\LgaController as AdminLgaController;
use App\Http\Controllers\Admin\MealController as AdminMealController;
use App\Http\Controllers\Admin\StateController as AdminStateController;
use App\Http\Controllers\Admin\SubcategoryController as AdminSubcategoryController;
use App\Http\Controllers\Admin\TownController as AdminTownController;
use App\Http\Controllers\Guest\CartController;
use App\Http\Controllers\Guest\CategoryController;
use App\Http\Controllers\Guest\LgaController;
use App\Http\Controllers\Guest\MealController;
use App\Http\Controllers\Guest\StateController;
use App\Http\Controllers\Guest\SubcategoryController;
use App\Http\Controllers\Guest\TownController;
use App\Http\Controllers\User\LgaController as UserLgaController;
use App\Http\Controllers\User\MealController as UserMealController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\StateController as UserStateController;
use App\Http\Controllers\User\TownController as UserTownController;
use App\Http\Controllers\User\UserController;
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

  //guest meal route
  Route::post('meal/list', [MealController::class, 'index']);

  //guest cart route
  Route::post('cart/new', [CartController::class, 'store']);
  Route::post('cart/sync', [CartController::class, 'sync_cart']);


  //guest state route
  Route::get('state/list', [StateController::class, 'index']);
  //guest lga route
  Route::get('lga/list', [LgaController::class, 'index']);
  //guest town route
  Route::get('town/list', [TownController::class, 'index']);
  //guest category route
  Route::get('category/list', [CategoryController::class, 'index']);

  //guest subcategory route
  Route::get('subcategory/list', [SubcategoryController::class, 'index']);
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



  //admin category route
  Route::group(['prefix' => 'dispatcher'], function () {
    Route::get('list', [AdminDispatcherController::class, 'index']);
    Route::post('block/{dispatcher_code}', [AdminDispatcherController::class, 'block'])->where('dispatcher_code', '[a-z0-9-]+');
    Route::post('activate/{dispatcher_code}', [AdminDispatcherController::class, 'activate'])->where('dispatcher_code', '[a-z0-9-]+');
  });


  //admin meal route
  Route::group(['prefix' => 'meal'], function () {
    Route::get('list', [AdminMealController::class, 'index']);
    Route::get('make_available/{meal_slug}', [AdminMealController::class, 'make_available'])->where('meal_slug', '[a-z0-9-]+');
    Route::get('make_unavailable/{meal_slug}', [AdminMealController::class, 'make_unavailable'])->where('meal_slug', '[a-z0-9-]+');
    Route::get('remove_extra_item/{meal_slug}/{extra_item_slug}', [AdminMealController::class, 'remove_single_extra_item'])->where(['meal_slug' => '[a-z0-9-]+', 'extra_item_slug' => '[a-z0-9-]+']);
    Route::get('remove_extra_item/{meal_slug}', [AdminMealController::class, 'remove_all_extra_item'])->where(['meal_slug' => '[a-z0-9-]+']);
    Route::get('make_unavailable/{meal_slug}', [AdminMealController::class, 'make_unavailable'])->where(['meal_slug' => '[a-z0-9-]+', 'extra_item_slug' => '[a-z0-9-]+']);
    Route::post('new', [AdminMealController::class, 'store']);
    Route::post('update', [AdminMealController::class, 'update']);
  });


  //admin category route
  Route::group(['prefix' => 'category'], function () {
    Route::get('list', [AdminCategoryController::class, 'index']);
    Route::post('new', [AdminCategoryController::class, 'store']);
    Route::post('update', [AdminCategoryController::class, 'update']);
  });

  //admin subcategory route
  Route::group(['prefix' => 'subcategory'], function () {
    Route::get('list', [AdminSubcategoryController::class, 'index']);
    Route::post('new', [AdminSubcategoryController::class, 'store']);
    Route::post('update', [AdminSubcategoryController::class, 'update']);
  });

  //admin state route
  Route::group(['prefix' => 'state'], function () {
    Route::get('list', [AdminStateController::class, 'index']);
    Route::get('enable/{state_slug}', [AdminStateController::class, 'enable'])->where('state_slug', '[a-z0-9-]+');
    Route::get('disable/{state_slug}', [AdminStateController::class, 'disable'])->where('state_slug', '[a-z0-9-]+');
  });

  //admin lga route
  Route::group(['prefix' => 'lga'], function () {
    Route::get('list', [LgaController::class, 'index']);
    Route::get('enable/{lga_slug}', [AdminLgaController::class, 'enable'])->where('state_slug', '[a-z0-9-]+');
    Route::get('disable/{lga_slug}', [AdminLgaController::class, 'disable'])->where('state_slug', '[a-z0-9-]+');
  });

  //admin town route
  Route::group(['prefix' => 'town'], function () {
    Route::get('list', [AdminTownController::class, 'index']);
  });
});


//user routes
Route::group(['prefix' => 'user'], function () {

  //user auth route
  Route::group(['prefix' => 'auth'], function () {
    //user registration route
    Route::post('register/default', [UserController::class, 'default_register']);
    //user lgin route
    Route::post('login/default', [UserController::class, 'default_login']);
  });


  //user order route
  Route::group(['prefix' => 'order'], function () {
    Route::post('new', [OrderController::class, 'store']);
  });

  //user meal route
  Route::group(['prefix' => 'meal'], function () {
    Route::get('list', [UserMealController::class, 'index']);
  });

  //user state route
  Route::group(['prefix' => 'state'], function () {
    Route::get('list', [UserStateController::class, 'index']);
  });

  //user lga route
  Route::group(['prefix' => 'lga'], function () {
    Route::get('list', [UserLgaController::class, 'index']);
  });

  //user town route
  Route::group(['prefix' => 'town'], function () {
    Route::get('list', [UserTownController::class, 'index']);
  });
});
