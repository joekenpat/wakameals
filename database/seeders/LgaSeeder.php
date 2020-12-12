<?php

namespace Database\Seeders;

use App\Models\Lga;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class LgaSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    try {
      $PHPArrayReader = new ReadLgaCSV();
      $lga_count = $PHPArrayReader->countPhpLargeArrayData();
      $lgaProgressBar = $this->command->getOutput()->createProgressBar($lga_count);
      $lgaProgressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% ');
      DB::statement('SET FOREIGN_KEY_CHECKS  = 0;');
      DB::disableQueryLog();
      foreach ($PHPArrayReader->readPhpLargeArrayData() as $data) {
        Lga::insertOrIgnore($data);
        $lgaProgressBar->advance(count($data));
      }
      DB::statement('SET FOREIGN_KEY_CHECKS  = 1;');
      DB::enableQueryLog();
      $lgaProgressBar->finish();
    } catch (QueryException $qe) {
      if ($qe->errorInfo[0] == "23000" && $qe->errorInfo[1] == "1062") {
        Log::error(dd($qe->errorInfo));
      }
    }
  }
}
