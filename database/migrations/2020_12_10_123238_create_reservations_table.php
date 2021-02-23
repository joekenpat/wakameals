<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('reservations', function (Blueprint $table) {
      $table->id();
      $table->string('code', 10)->unique();
      $table->string('name');
      $table->string('phone');
      $table->string('email');
      $table->text('address')->nullable()->default(null);
      $table->text('event_address')->nullable()->default(null);
      $table->string('event_type')->nullable()->default(null);;
      $table->string('service_type');
      $table->string('crowd_type')->nullable();
      $table->string('menu_type')->nullable()->default(null);;
      $table->integer('no_of_persons')->default(1);
      $table->unsignedBigInteger('dispatcher_id')->nullable()->default(null);
      $table->unsignedBigInteger('place_id')->nullable()->default(null);
      $table->string('status', 40);
      $table->timestamp('reserved_at', 6)->useCurrent()->nullable();
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
    Schema::dropIfExists('reservations');
  }
}
