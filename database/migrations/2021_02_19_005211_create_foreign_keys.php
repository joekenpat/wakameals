<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeignKeys extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('subcategories', function (Blueprint $table) {
      $table->foreign('category_id')->references('id')->on('categories');
    });

    Schema::table('meals', function (Blueprint $table) {
      $table->foreign('category_id')->references('id')->on('categories');
      $table->foreign('subcategory_id')->references('id')->on('subcategories');
    });

    Schema::table('users', function (Blueprint $table) {
      $table->foreign('place_id')->references('id')->on('places');
    });

    Schema::table('admins', function (Blueprint $table) {
      $table->foreign('place_id')->references('id')->on('places');
    });

    Schema::table('chefs', function (Blueprint $table) {
      $table->foreign('place_id')->references('id')->on('places');
      $table->foreign('dispatcher_id')->references('id')->on('dispatchers');
    });

    Schema::table('dispatchers', function (Blueprint $table) {
      $table->foreign('place_id')->references('id')->on('places');
    });

    Schema::table('orders', function (Blueprint $table) {
      $table->foreign('place_id')->references('id')->on('places');
      $table->foreign('user_id')->references('id')->on('users');
      $table->foreign('dispatcher_id')->references('id')->on('dispatchers');
      $table->foreign('chef_id')->references('id')->on('chefs');
    });

    Schema::table('transactions', function (Blueprint $table) {
      $table->foreign('user_id')->references('id')->on('users');
    });

    Schema::table('meal_extra_items', function (Blueprint $table) {
      $table->foreign('extra_item_id')->references('id')->on('extra_items');
      $table->foreign('meal_id')->references('id')->on('meals');
    });

    Schema::table('ordered_meals', function (Blueprint $table) {
      $table->foreign('order_id')->references('id')->on('orders');
      $table->foreign('meal_id')->references('id')->on('meals');
    });

    Schema::table('ordered_meal_extra_items', function (Blueprint $table) {
      $table->foreign('ordered_meal_id')->references('id')->on('ordered_meals');
      $table->foreign('meal_extra_item_id')->references('id')->on('meal_extra_items');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('subcategories', function (Blueprint $table) {
      $table->dropForeign('category_id');
    });

    Schema::table('meals', function (Blueprint $table) {
      $table->dropForeign('category_id');
      $table->dropForeign('subcategory_id');
    });

    Schema::table('users', function (Blueprint $table) {
      $table->dropForeign('place_id');
    });

    Schema::table('admins', function (Blueprint $table) {
      $table->dropForeign('place_id');
    });

    Schema::table('chefs', function (Blueprint $table) {
      $table->dropForeign('place_id');
      $table->dropForeign('dispatcher_id');
    });

    Schema::table('dispatchers', function (Blueprint $table) {
      $table->dropForeign('place_id');
    });

    Schema::table('orders', function (Blueprint $table) {
      $table->dropForeign('place_id');
      $table->dropForeign('user_id');
      $table->dropForeign('dispatcher_id');
      $table->dropForeign('chef_id');
    });

    Schema::table('transactions', function (Blueprint $table) {
      $table->dropForeign('user_id');
    });

    Schema::table('meal_extra_items', function (Blueprint $table) {
      $table->dropForeign('extra_item_id');
      $table->dropForeign('meal_id');
    });

    Schema::table('ordered_meals', function (Blueprint $table) {
      $table->dropForeign('order_id');
      $table->dropForeign('meal_id');
    });

    Schema::table('ordered_meal_extra_items', function (Blueprint $table) {
      $table->dropForeign('ordered_meal_id');
      $table->dropForeign('meal_extra_item_id');
    });
  }
}
