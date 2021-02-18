<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('admins', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('avatar')->nullable()->default(null);
      $table->string('first_name')->nullable()->default(null);
      $table->string('last_name')->nullable()->default(null);
      $table->string('phone', 25)->unique()->nullable()->default(null);
      $table->string('email')->unique();
      $table->string('status');
      $table->unsignedBigInteger('place_id')->nullable()->default(null);
      $table->timestamp('email_verified_at')->nullable();
      $table->ipAddress('last_ip');
      $table->string('password');
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
    Schema::dropIfExists('admins');
  }
}
