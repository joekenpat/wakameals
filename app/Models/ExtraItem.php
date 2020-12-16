<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ExtraItem extends Model
{
  use Sluggable, SoftDeletes;

  /**
   * set the attributes to slug from
   *
   * @var String
   */
  public $sluggable = 'name';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'slug',
    'price',
    'available',
    'measurement_quantity',
    'measurement_type',
  ];

  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';


  /**
   * The attributes that are hidden
   *
   * @var array
   */
  protected $hidden = [
    'created_at',
    'updated_at',
    'deleted_at',
    'pivot',
    'available'
  ];


  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */

  protected $casts = [
    'available' => 'boolean',
  ];

  public function GetAvailableMealsAttribute()
  {
    return $this->meals()->where('availability', true);
  }

  public function meals()
  {
    return $this->belongsToMany(Meal::class, 'meal_extra_items')->using(MealExtraItem::class);
  }
}
