<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\MealController as AdminMealController;
use App\Http\Controllers\Admin\SubcategoryController as AdminSubcategoryController;
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

  //admin meal route
  Route::group(['prefix' => 'meal'], function () {
    Route::get('list', [AdminMealController::class, 'index']);
    Route::post('new', [AdminMealController::class, 'store']);
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
});


//user routes
Route::group(['prefix' => 'user'], function () {

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
