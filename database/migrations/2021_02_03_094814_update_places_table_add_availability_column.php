<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlacesTableAddAvailabilityColumn extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('places', function (Blueprint $table) {
      $table->boolean('pickup_available')->after('name')->default(false);
      $table->boolean('delivery_available')->after('pickup_available')->default(false);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('places', function (Blueprint $table) {
      $table->dropColumn(['pickup_available', 'delivery_available']);
    });
  }
}
