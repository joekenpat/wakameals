<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
  protected $hidden = [
    'created_at',
    'deleted_at',
    'updated_at',
    'ordered_meal_id',
    'meal_extra_item_id',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */

  protected $casts = [];


  protected $appends = ['cost'];

  /**
   * appendable relationships
   *
   * @var array
   */

  protected $with = [
    'meal_extra_item',
  ];



  public function ordered_meal()
  {
    return $this->belongsTo(OrderedMeal::class);
  }

  public function meal_extra_item()
  {
    return $this->belongsTo(ExtraItem::class);
  }

  public function getCostAttribute()
  {
    $extra_item = ExtraItem::whereId($this->meal_extra_item_id)->firstOrFail();
    return $this->quantity * $extra_item->price;
  }


}
