<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UuidForKey
{
  /**
   * Boot the Uuid trait for the model.
   *
   * @return void
   */
  public static function bootUuidForKey()
  {
    static::creating(function ($model) {
      $model->incrementing = false;
      if ($model->{$model->getKeyName()} === null || $model->{$model->getKeyName()} === '') {
        $model->{$model->getKeyName()} = Str::orderedUuid();
      }
    });
  }

  /**
   * Get the casts array.
   *
   * @return array
   */
  public function getCasts()
  {
    return $this->casts;
  }
}
