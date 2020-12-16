<?php

namespace App\Services\MealFilters;

use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class Status implements Filter
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
      'status' => 'required|alpha_dash|in:created,cancelled,completed,dispatched,confirmed',
    ];
    $valid_value = ['status' => $value];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      return $builder
        ->where('status', $value);
    } else {
      return $builder;
    }
  }
}
