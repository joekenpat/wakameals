<?php

namespace App\Models;

use App\Traits\Sluggable;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
  use SoftDeletes, Sluggable;

  /**
   * set the attributes to slug from
   *
   * @var String
   */
  public $sluggable = 'name';

  const filterables = [
    'name', 'enabled',
  ];


  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'name', 'slug', 'enabled',
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
  ];

  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';

  public function orders()
  {
    return $this->hasMany(Order::class, 'place_id');
  }

  public function users()
  {
    return $this->hasMany(User::class, 'place_id');
  }

  public function admins()
  {
    return $this->hasMany(Admin::class, 'place_id');
  }
  public function dispatchers()
  {
    return $this->hasMany(Dispatcher::class, 'place_id');
  }


  public function disable()
  {
    $this->update(['enabled' => false]);
  }

  public function enable()
  {
    $this->update(['enabled' => true]);
  }
}
