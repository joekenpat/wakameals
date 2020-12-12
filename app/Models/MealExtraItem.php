<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Dyrynda\Database\Casts\EfficientUuid;

class MealExtraItem extends Model
{


  public function uuidColumns(): array
  {
    return ['meal_id'];
  }

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'meal_id',
    'price',
    'availability',
    'measurement',
  ];

  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */

  protected $casts = [
    'email_verified_at' => 'datetime',
    'id' => EfficientUuid::class,
    'meal_id' => EfficientUuid::class,
    'availability' => 'boolean',
  ];

  public function meal()
  {
    return $this->belongsTo(Meal::class);
  }
}
