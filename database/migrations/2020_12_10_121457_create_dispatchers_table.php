<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatchersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('dispatchers', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('code', 6)->unique();
      $table->string('avatar')->nullable()->default(null);
      $table->string('name')->nullable()->default(null);
      $table->string('phone', 11)->unique()->nullable()->default(null);
      $table->string('email')->unique();
      $table->string('status', 40);
      $table->string('type', 40);
      $table->unsignedBigInteger('state_id')->nullable()->default(null);
      $table->unsignedBigInteger('lga_id')->nullable()->default(null);
      $table->unsignedBigInteger('town_id')->nullable()->default(null);
      $table->timestamp('email_verified_at')->nullable();
      $table->ipAddress('last_ip');
      $table->string('password');
      $table->text('address')->nullable()->default(null);
      $table->rememberToken();
      $table->timestamp('last_login', 6)->nullable()->default(null);
      $table->timestamp('blocked_at', 6)->nullable()->default(null);
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
    Schema::dropIfExists('dispatchers');
  }
}
