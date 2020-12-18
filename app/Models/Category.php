<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

  use SoftDeletes;

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
    'name', 'icon', 'slug',
  ];

  /**
   * The attributes that are countable.
   *
   * @var array
   */
  protected $withCount = [
    'subcategories', 'meals'
  ];
  /**
   * Realations.
   *
   * @var array
   */
  protected $with = [];

  /**
   * The attributes that are hidden
   *
   * @var array
   */
  protected $hidden = [
    'created_at',
    'updated_at',
    'deleted_at',
  ];


  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';

  public function meals()
  {
    return $this->hasMany(Meal::class);
  }

  public function subcategories()
  {
    return $this->hasMany(Subcategory::class);
  }

  public function getIconAttribute($value)
  {
    return $this->attributes['icon'] !== null ? asset('images/categories/' . $this->icon) : null;
  }

  /**
   * The "booted" method of this model.
   *
   * @return void
   */
  protected static function booted()
  {
    static::saving(function ($model) {
      $model_slug = Str::slug($model->attributes['name']);
      if (!static::where('id', '!=', $model->id)->where('slug', $model_slug)->withTrashed()->exists()) {
        $model->slug = $model_slug;
      } else {
        $count = 1;
        while (static::where('id', '!=', $model->id)->where('slug', "{$model_slug}-" . $count)->withTrashed()->exists()) {
          $count++;
        }
        $model->slug = "{$model_slug}-" . $count;
      }
    });
  }
}
