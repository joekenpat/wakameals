<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Subcategory::create([
      'name' => 'Test Subcategory',
      'category_id' => Category::first()->id,
    ]);
  }
}
