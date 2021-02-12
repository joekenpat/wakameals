<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('orders', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('code', 10)->unique();
      $table->string('dispatch_code', 10)->unique()->nullable()->default(null);
      $table->uuid('user_id');
      $table->uuid('chef_id')->nullable()->default(null);
      $table->uuid('dispatcher_id')->nullable()->default(null);
      $table->unsignedBigInteger('place_id')->nullable()->default(null);
      $table->string('status', 40);
      $table->string('delivery_type', 40);
      $table->string('type', 40);
      $table->text('address')->nullable();
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
    Schema::dropIfExists('orders');
  }
}
