<?php

namespace App\Services\TownFilters;

use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Models\Lga as ModelsLga;

class Lga implements Filter
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
      'lga' => 'required|alpha_dash|exists:lgas,slug',
    ];
    $valid_value = ['lga' => $value];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      $lga = ModelsLga::whereSlug($value)->firstOrFail();
      return $builder
        ->where('lga_id', $lga->id);
    } else {
      return $builder;
    }
  }
}
