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
   * The relationship that are appendable.
   *
   * @var array
   */

  protected $with = [
    'meal', 'ordered_meal_extra_items',
  ];

  protected $appends = ['cost'];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'created_at',
    'deleted_at',
    'updated_at',
    'order_id',
    // 'meal_id',
  ];

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
    return $this->belongsTo(Meal::class, 'meal_id');
  }

  public function ordered_meal_extra_items()
  {
    return $this->hasMany(OrderedMealExtraItem::class);
  }

  public function getCostAttribute()
  {
    $meal = Meal::whereId($this->meal_id)->firstOrFail();
    $ordered_extra_items = OrderedMealExtraItem::whereOrderedMealId($this->id)->get();
    $cost = $meal->price;
    foreach ($ordered_extra_items as $item) {
      $cost += $item->cost;
    }
    return $cost;
  }
}
