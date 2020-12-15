<?php

namespace App\Services\MealFilters;

use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class Available  implements Filter
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
      'available' => 'boolean',
    ];
    $valid_value = ['available' => $value];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      return $builder
        ->whereAvailable($value);
    } else {
      return $builder;
    }
  }
}
