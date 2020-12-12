<?php

namespace Database\Seeders;

use App\Models\Town;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class TownSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $PHPArrayReader = new ReadTownCSV();
    DB::statement('SET FOREIGN_KEY_CHECKS  = 0;');
    DB::disableQueryLog();
    $city_count = $PHPArrayReader->countPhpLargeArrayData();
    $cityProgressBar = $this->command->getOutput()->createProgressBar($city_count);
    $cityProgressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% ');
    foreach ($PHPArrayReader->readPhpLargeArrayData() as $data) {
      Town::insertOrIgnore($data);
      $cityProgressBar->advance(count($data));
      $data = [];
    }
    DB::enableQueryLog();
    DB::statement('SET FOREIGN_KEY_CHECKS  = 1;');
  }
}
