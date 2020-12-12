<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
  const filterables = [
    'name', 'state', 'lga',
  ];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'name', 'slug', 'state_id',
    'lga_id',
  ];

  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';

  public function orders()
  {
    return $this->hasMany(Order::class, 'town_id');
  }

  public function users()
  {
    return $this->hasMany(User::class, 'town_id');
  }

  public function admins()
  {
    return $this->hasMany(Admin::class, 'town_id');
  }

  public function lgas()
  {
    return $this->hasMany(Lga::class, 'town_id');
  }

  public function state()
  {
    return $this->belongsTo(State::class, 'state_id');
  }

  public function lga()
  {
    return $this->belongsTo(Lga::class, 'lga_id');
  }
}
