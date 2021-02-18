<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Admin::create([
      'first_name' => 'Test',
      'last_name' => 'Admin',
      'status' => 'active',
      'email' => 'joekenpat@gmail.com',
      'phone' => '08174310668',
      'password' => Hash::make('joeslim1'),
      'place_id' => 1,
      'last_ip' => '127.0.0.1',
      'last_login' => now(),
      'blocked_at' => null,
    ]);
    Admin::create([
      'first_name' => 'Inmotion',
      'last_name' => 'Hub',
      'status' => 'active',
      'email' => 'inmotionicthub@gmail.com',
      'phone' => '09060400096',
      'password' => Hash::make('$1qaz2wsx#'),
      'place_id' => 1,
      'last_ip' => '127.0.0.1',
      'last_login' => now(),
      'blocked_at' => null,
    ]);
  }
}
