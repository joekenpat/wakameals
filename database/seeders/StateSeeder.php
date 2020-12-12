<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StateSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $PHPArrayReader = new ReadStateCSV();
    $state_count = $PHPArrayReader->countPhpLargeArrayData();
    $stateProgressBar = $this->command->getOutput()->createProgressBar($state_count);
    $stateProgressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% ');
    DB::statement('SET FOREIGN_KEY_CHECKS  = 0;');
    DB::disableQueryLog();
    foreach ($PHPArrayReader->readPhpLargeArrayData() as $data) {
      State::insert($data);
      $stateProgressBar->advance(count($data));
    }
    DB::statement('SET FOREIGN_KEY_CHECKS  = 1;');
    DB::enableQueryLog();
    $stateProgressBar->finish();
  }
}
