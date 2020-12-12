<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

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
        'meals', 'subcategories',
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

    public function getIconAttribute()
    {
        return $this->icon !== null ? asset('images/categories/' . $this->icon) : null;
    }
}
