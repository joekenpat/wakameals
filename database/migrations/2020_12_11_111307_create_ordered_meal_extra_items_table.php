<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderedMealExtraItemsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('ordered_meal_extra_items', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('ordered_meal_id');
      $table->unsignedBigInteger('meal_extra_item_id');
      $table->integer('quantity')->default(0);
      $table->string('status', 40);
      $table->timestamp('created_at', 6)->useCurrent();
      $table->timestamp('updated_at', 6)->useCurrent()->nullable();
      $table->timestamp('deleted_at', 6)->nullable()->default(null);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('ordered_meal_extra_items');
  }
}
