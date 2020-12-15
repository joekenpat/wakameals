<?php

namespace App\Services\MealFilters;

use App\Models\Category as ModelsCategory;
use App\Services\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class Category implements Filter
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
      'category' => 'required|alpha_dash|exists:categories,slug',
    ];
    $valid_value = ['category' => $value];
    $validator = Validator::make($valid_value, $rules);
    if (!$validator->fails()) {
      $category = ModelsCategory::whereSlug($value)->firstOrFail();
      return $builder
        ->where('category_id', $category->id);
    } else {
      return $builder;
    }
  }
}
