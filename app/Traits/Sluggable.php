<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
  /**
   * Boot the Set Slug Attribute trait for the model.
   *
   * @return void
   */
  public static function bootSluggable()
  {
    static::saving(function ($model) {
      $source = $model->sluggable;
      $model_slug = Str::slug($model->attributes[$source]);
      if (!static::where('id', '!=', $model->id)->where('slug', $model_slug)->withTrashed()->exists()) {
        $model->slug = $model_slug;
      } else {
        $count = 1;
        while (static::where('id', '!=', $model->id)->where('slug', "{$model_slug}-" . $count)->withTrashed()->exists()) {
          $count++;
        }
        $model->slug = "{$model_slug}-" . $count;
      }
    });
  }
}
