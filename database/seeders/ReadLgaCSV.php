<?php

namespace Database\Seeders;

use Illuminate\Support\Str;

include(resource_path() . '/db_data/ng_lgas.php');

class ReadLgaCSV
{
  public function __construct()
  {
    $this->iterator = 0;
  }


  public function readPhpLargeArrayData()
  {
    $lgas = get_lgas();
    $data = [];
    $state_count = count(get_lgas());
    $i = 0;
    while ($i < $state_count) {
      $is_mul_20 = false;
      $this->iterator++;
      $lga = $lgas[$i];
      $lga['slug'] = Str::slug($lga['name']);
      $data[] = $lga;
      if ($this->iterator != 0 && $this->iterator % 20 == 0) {
        $is_mul_20 = true;
        $chunk = $data;
        $data = [];
        yield $chunk;
      }
      $i++;
    }
    if (!$is_mul_20) {
      yield $data;
    }
    return;
  }

  public function countPhpLargeArrayData()
  {
    // include(resource_path() . '/db_data/ng_lgas.php');
    return count(get_lgas());
  }
}
