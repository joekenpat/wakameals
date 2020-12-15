<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
  use Sluggable;

  /**
   * set the attributes to slug from
   *
   * @var String
   */
  public $sluggable = 'name';

  const filterables = [
    'name', 'state', 'enabled',
  ];
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'slug', 'state_id', 'enabled',
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
    return $this->hasMany(Order::class, 'lga_id');
  }

  public function users()
  {
    return $this->hasMany(User::class, 'lga_id');
  }

  public function admins()
  {
    return $this->hasMany(Admin::class, 'lga_id');
  }
  public function dispatchers()
  {
    return $this->hasMany(Dispatcher::class, 'lga_id');
  }

  public function lgas()
  {
    return $this->hasMany(Lga::class, 'lga_id');
  }

  public function towns()
  {
    return $this->hasMany(town::class, 'lga_id');
  }

  public function state()
  {
    return $this->belongsTo(State::class, 'state_id');
  }

  public function disable()
  {
    $this->towns()->update(['enabled' => false]);
    $this->update(['enabled' => false]);
  }

  public function enabled()
  {
    $this->towns()->update(['enabled' => true]);
    $this->update(['enabled' => true]);
  }
}
