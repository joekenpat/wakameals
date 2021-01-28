<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReservationTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('table_reservations', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('code', 6)->unique();
      $table->uuid('user_id');
      $table->uuid('dispatcher_id')->nullable()->default(null);
      $table->unsignedBigInteger('place_id')->nullable()->default(null);
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
    Schema::dropIfExists('orders');
  }
}
