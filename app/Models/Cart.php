<?php

namespace App\Models;

use App\Traits\UuidForKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */

  public  $incrementing = false;
  protected $fillable = [
    'name',
    'user_id',
    'meal_id',
    'special_instruction',
    'meal_extras',
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
    // 'meal_extra_items'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'meal_extras',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */

  protected $casts = [
    'meal_extras' => 'array'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function meal()
  {
    return $this->hasOne(Meal::class);
  }

  // public function getMealExtraItemsAttribute()
  // {
  //   $meal_extrx = $this->fil['meal_extras'];
  //   $padded_meal_extras = [];
  //   foreach ($meal_extrx as $me) {
  //     $padded_meal_extras[] = $this->meal_extra_item_with_cost($me->id, $me->quantity);
  //   }
  //   return $padded_meal_extras;
  // }


  // public function getSubTotalAttribute()
  // {
  //   $meal = $this->meal();
  //   $meal_cost = $meal->price ?: 0;
  //   $extra_items_cost = 0;
  //   foreach ($this->meal_extra_items as $meal_item) {
  //     $extra_items_cost += $meal_item->sub_cost ?: 0;
  //   }
  //   return $meal_cost + $extra_items_cost;
  // }
}
