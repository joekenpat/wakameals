<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{

  const filterables = [
    'name', 'state',
  ];
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'slug', 'lga_id',
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
}
