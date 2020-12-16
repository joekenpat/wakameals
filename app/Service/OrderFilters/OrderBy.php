<?php

namespace App\Services\MealFilters;

use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class OrderBy implements Filter
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
      'order_by' => 'required|alpha_dash|in:old_new,new_old',
    ];
    $valid_value = ['status' => $value];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      if ($value == 'old_new') {
        return $builder
          ->oldest();
      } else {
        return $builder
          ->latest();
      }
    } else {
      return $builder;
    }
  }
}
