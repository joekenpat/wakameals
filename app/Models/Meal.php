<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\UuidForKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Meal extends Model
{

  use UuidForKey, SoftDeletes, Sluggable;

  /**
   * set the attributes to slug from
   *
   * @var String
   */
  public $sluggable = 'name';

  const filterables = [
    'name', 'category', 'subcategory',
    'max_price', 'min_price', 'available',
  ];


  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'slug',
    'price',
    'image',
    'available',
    'category_id',
    'subcategory_id',
    'measurement_quantity',
    'measurement_type',
    'description',
  ];

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
    'available',
    'category_id',
    'subcategory_id',
  ];

  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */

  protected $casts = [
    'available' => 'boolean',
  ];

  protected $with = [
    // 'category', 'subcategory',
    'extra_items',
  ];


  public function subcategory()
  {
    return $this->belongsTo(Subcategory::class, 'subcategory_id');
  }

  public function category()
  {
    return $this->belongsTo(Category::class, 'category_id');
  }

  public function GetAvailableExtraItemsAttribute()
  {
    return $this->extra_items()->where('availability', true);
  }

  public function extra_items()
  {
    return $this->belongsToMany(ExtraItem::class, 'meal_extra_items')->using(MealExtraItem::class);
  }

  public function ordered_meals()
  {
    return $this->belongsToMany(OrderedMeal::class, 'meal_id');
  }

  public function getImageAttribute($value)
  {
    return $value == null ? null : asset('images/meals/' . $value);
  }

  public function make_available()
  {
    $this->update(['available' => true]);
  }

  public function make_unavailable()
  {
    $this->update(['available' => false]);
  }

  public function remove_single_extra_item(Int $extra_item_id)
  {
    $this->extra_items()->detach($extra_item_id);
  }

  public function remove_all_extra_item()
  {
    $this->extra_items()->detach();
  }
}
