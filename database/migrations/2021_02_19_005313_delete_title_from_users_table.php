<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteTitleFromUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropColumn('title');
    });
    Schema::table('admins', function (Blueprint $table) {
      $table->dropColumn('title');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->char('title', 10)->nullable()->default('mr');
    });
    Schema::table('admins', function (Blueprint $table) {
      $table->char('title', 10)->nullable()->default('mr');
    });
  }
}
