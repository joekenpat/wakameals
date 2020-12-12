<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Dyrynda\Database\Casts\EfficientUuid;

class OrderedMealExtraItem extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'ordered_meal_id',
    'meal_extra_item_id',
    'quantity',
    'status'
  ];

  /**
   * The attributes that are appendable.
   *
   * @var array
   */

  protected $appends = [
    'cost'
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

  protected $casts = [];

  public function ordered_meal()
  {
    return $this->belongsTo(OrderedMeal::class);
  }

  public function meal_extra_item()
  {
    return $this->belongsTo(MealExtraItem::class);
  }

  public function getCostAttribute(): int
  {
    $cost = 0;
    $extra_item = $this->meal_extra_item();
    $cost += $extra_item->price * $this->quantity;
    return $cost;
  }
}
