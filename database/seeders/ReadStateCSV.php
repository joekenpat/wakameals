<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

include(resource_path() . '/db_data/ng_states.php');

class ReadStateCSV
{

  public function __construct()
  {
    $this->iterator = 0;
  }


  public function readPhpLargeArrayData()
  {
    $states = get_states();
    $data = [];
    $state_count = count(get_states());
    $i = 0;
    while ($i < $state_count) {
      $is_mul_20 = false;
      $this->iterator++;
      $state = $states[$i];
      $state['slug'] = Str::slug($state['name']);
      $data[] = $state;
      if ($this->iterator != 0 && $this->iterator % 50 == 0) {
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
    // include(resource_path() . '/db_data/ng_states.php');
    return count(get_states());
  }
}
