<?php

namespace App\Services\LgaFilters;

use App\Models\State as ModelsState;
use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

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
      $state = ModelsState::whereSlug($value)->firstOrFail();
      return $builder
        ->where('state_id', $state->id);
    } else {
      return $builder;
    }
  }
}
