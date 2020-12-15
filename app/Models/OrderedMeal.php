<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderedMeal extends Model
{

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'order_id',
    'meal_id',
    'special_instruction',
    'status',
  ];

  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';

  /**
   * The attributes that are appendable.
   *
   * @var array
   */

  protected $appends = [
    'sub_total'
  ];

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

  public function order()
  {
    return $this->belongsTo(Order::class);
  }

  public function meal()
  {
    return $this->belongsTo(Meal::class);
  }

  public function ordered_extra_items()
  {
    return $this->hasMany(OrderedMealExtraItem::class);
  }

  public function getSubTotalAttribute(): int
  {
    $cost = 0;
    $meal_price = $this->meal()->price;
    $extras_cost = 0;
    foreach ($this->ordered_meal_extra_items() as $meal_extra_item) {
      $extras_cost += $meal_extra_item->cost;
    }
    $cost += $meal_price + $this->quantity;
    return $cost;
  }
}
