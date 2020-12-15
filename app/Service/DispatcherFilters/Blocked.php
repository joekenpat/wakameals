<?php

namespace App\Services\TownFilters;

use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class Blocked  implements Filter
{

  /**
   * Apply a given search value to the builder instance.
   *
   * @param Builder $builder
   * @param mixed $value
   * @return Builder $builder
   */
  public static function apply(Builder $builder, $value)
  {
    $rules = [
      'enabled' => 'boolean',
    ];
    $valid_value = ['enabled' => $value];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      if ($value == true) {
        return $builder
          ->whereNotNull('blocked_at');
      } else {
        return $builder
          ->whereNull('blocked_at');
      }
    } else {
      return $builder;
    }
  }
}
