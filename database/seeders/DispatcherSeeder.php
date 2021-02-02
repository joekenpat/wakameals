<?php

namespace Database\Seeders;

use App\Models\Dispatcher;
use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DispatcherSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Dispatcher::create([
      'name' => 'Joel Patrick',
      'status' => 'active',
      'email' => 'joekenpat@gmail.com',
      'type' => 'pickup',
      'phone' => '08174310668',
      'password' => Hash::make('joeslim1'),
      'place_id' => Place::first()->id,
      'address' => 'Nigeria',
      'last_ip' => '127.0.0.1',
      'last_login' => now(),
      'blocked_at' => null,
    ]);
    Dispatcher::create([
      'name' => 'Inmotion Hub',
      'status' => 'active',
      'email' => 'inmotionicthub@gmail.com',
      'type' => 'pickup',
      'phone' => '09060400096',
      'password' => Hash::make('$1qaz2wsx#'),
      'place_id' => Place::first()->id,
      'address' => 'Rivers State, Port Harcourt, Nkpolu',
      'last_ip' => '127.0.0.1',
      'last_login' => now(),
      'blocked_at' => null,
    ]);
  }
}
