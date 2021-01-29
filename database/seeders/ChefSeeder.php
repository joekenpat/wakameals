<?php

namespace Database\Seeders;

use App\Models\Chef;
use App\Models\Dispatcher;
use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ChefSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Chef::create([
      'name' => 'Joel Patrick',
      'status' => 'created',
      'email' => 'joekenpat@gmail.com',
      'phone' => '08174310668',
      'password' => Hash::make('joeslim1'),
      'place_id' => Place::first()->id,
      'dispatcher_id' => Dispatcher::first()->id,
      'last_ip' => '127.0.0.1',
      'last_login' => now(),
      'blocked_at' => null
    ]);
  }
}
