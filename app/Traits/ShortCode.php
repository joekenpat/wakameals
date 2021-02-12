<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ShortCode
{
  /**
   * Boot the Set Slug Attribute trait for the model.
   *
   * @return void
   */
  public function AddShortCode()
  {
    $configs = $this->shortCodeConfig;
    foreach ($configs as $config) {
      $short_code = Self::gen_short_code($config['length']);
      # code...
      if ($config['unique']) {
        if (!$this->where('id', '!=', $this->id)->where($config['column_name'], $short_code)->withTrashed()->exists()) {
          $this->{$config['column_name']} = $short_code;
        } else {
          while ($this->where('id', '!=', $this->id)->where($config['column_name'], $short_code)->withTrashed()->exists()) {
            $short_code = Self::gen_short_code($config['length']);
          }
          $this->{$config['column_name']} = $short_code;
        }
      } else {
        $this->{$config['column_name']} = $short_code;
      }
    }
  }

  /**
   * Generate short Alphanumeric codes.
   * @return String
   */
  public static function gen_short_code($length = 8): string
  {
    $min = ("1" . str_repeat(0, ($length - 1)));
    $max = ("9" . str_repeat(0, ($length - 1)));
    return random_int($min, $max);
  }
}
