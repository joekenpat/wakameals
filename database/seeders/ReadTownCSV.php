<?php

namespace Database\Seeders;

use Illuminate\Support\Str;

include(resource_path() . '/db_data/ng_towns.php');

class ReadTownCSV
{

  public function __construct()
  {
    $this->iterator = 0;
  }


  public function readPhpLargeArrayData()
  {
    $towns = get_towns();
    $data = [];
    $state_count = count(get_towns());
    $i = 0;
    while ($i < $state_count) {
      $is_mul_20 = false;
      $this->iterator++;
      $town = $towns[$i];
      $town['slug'] = Str::slug($town['name']);
      $data[] = $town;
      if ($this->iterator != 0 && $this->iterator % 500 == 0) {
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
    // include(resource_path() . '/db_data/ng_towns.php');
    return count(get_towns());
  }
}
