<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Dyrynda\Database\Casts\EfficientUuid;

class Meal extends Model
{

  use GeneratesUuid;

  public function uuidColumn(): string
  {
    return 'id';
  }

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
    'availability',
    'category_id',
    'subcategory_id',
    'measurement',
    'description',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [];

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
    'id' => EfficientUuid::class,
    'availability' => 'boolean',
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
    return $this->hasMany(MealExtraItem::class);
  }
}
