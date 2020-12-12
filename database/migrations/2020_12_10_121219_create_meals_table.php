<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('meals', function (Blueprint $table) {
      $table->efficientUuid('id')->primary();
      $table->unsignedBigInteger('subcategory_id');
      $table->unsignedBigInteger('category_id');
      $table->string('slug')->unique();
      $table->string('name');
      $table->decimal('price');
      $table->string('image');
      $table->boolean('availability');
      $table->string('measurement');
      $table->text('description');
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
    Schema::dropIfExists('meals');
  }
}
