<?php

namespace App\Services\TownFilters;

use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Models\Place as ModelsState;

class State implements Filter
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
      'state' => 'required|alpha_dash|exists:states,slug',
    ];
    $valid_value = ['state' => $value];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      $state = ModelsPlace::whereSlug($value)->firstOrFail();
      return $builder
        ->where('state_id', $state->id);
    } else {
      return $builder;
    }
  }
}
