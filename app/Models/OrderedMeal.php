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
    'meal_id',
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
    return $this->belongsTo(Meal::class);
  }

  public function ordered_meal_extra_items()
  {
    return $this->hasMany(OrderedMealExtraItem::class);
  }
}
