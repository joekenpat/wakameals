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
      'first_name' => 'Joel',
      'last_name' => 'Patrick',
      'title' => 'mr',
      'status' => 'created',
      'email' => 'joekenpat@gmail.com',
      'phone' => '08174310668',
      'password' => Hash::make('joeslim1'),
      'place_id' => Place::first()->id,
      'address' => 'Nigeria',
      'last_ip' => '127.0.0.1',
      'last_login' => now(),
      'blocked_at' => null,
    ]);
  }
}
