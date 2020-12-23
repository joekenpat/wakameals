<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('{any}', function () {
  if (request()->segment(1) == 'dispatch') {
    return view('dispatcher');
  } elseif (request()->segment(1) == 'secured_admin') {
    return view('admin');
  } else {
    return view('user');
  }
})->where('any', '^((?!api).)*$');
