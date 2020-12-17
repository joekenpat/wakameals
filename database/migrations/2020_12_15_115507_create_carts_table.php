<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('carts', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('user_id');
      $table->uuid('meal_id');
      $table->string('name');
      $table->text('meal_extras')->nullable()->default(null);
      $table->text('special_instruction')->nullable()->default(null);
      $table->timestamp('created_at', 6)->useCurrent();
      $table->timestamp('updated_at', 6)->useCurrent()->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('carts');
  }
}
