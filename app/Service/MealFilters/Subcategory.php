<?php

namespace App\Services\MealFilters;

use App\Models\Subcategory as ModelsSubcategory;
use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class Subcategory implements Filter
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
      'subcategory' => 'required|alpha_dash|exists:subcategories,slug',
    ];
    $valid_value = ['subcategory' => $value];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      $subcategory = ModelsSubcategory::whereSlug($value)->firstOrFail();
      return $builder
        ->where('subcategory_id', $subcategory->id);
    } else {
      return $builder;
    }
  }
}
