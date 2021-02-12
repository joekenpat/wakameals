<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    User::create([
      'first_name' => 'Test',
      'last_name' => 'User',
      'title' => 'mr',
      'status' => 'active',
      'email' => 'joekenpat@gmail.com',
      'phone' => '08174310668',
      'password' => Hash::make('joeslim1'),
      'place_id' => Place::first()->id,
      'address' => 'Nigeria',
      'last_ip' => '127.0.0.1',
      'last_login' => now(),
      'blocked_at' => null,
    ]);
    User::create([
      'first_name' => 'Inmotion',
      'last_name' => 'Hub',
      'title' => 'mr',
      'status' => 'active',
      'email' => 'inmotionicthub@gmail.com',
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
