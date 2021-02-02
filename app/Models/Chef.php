<?php

namespace App\Models;

use App\Traits\UuidForKey;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Chef extends Authenticatable
{
  use Notifiable, HasApiTokens, UuidForKey, SoftDeletes;


  const filterables = [
    'name', 'place', 'blocked',
  ];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'avatar',
    'name',
    'email',
    'password',
    'status',
    'place_id',
    'dispatcher_id',
    'last_ip',
    'last_login',
    'blocked_at',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
    'last_ip',
    'created_at',
    'updated_at',
    'deleted_at',
    'email_verified_at',
  ];

  /**
   * Relationships that are countables
   *
   * @var array
   */

  protected $withCount = [
    'prepared_orders',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */

  protected $casts = [
    'email_verified_at' => 'datetime',
    'blocked_at' => 'datetime',
  ];

  public function place()
  {
    return $this->belongsTo(Place::class, 'place_id');
  }

  public function dispatcher()
  {
    return $this->belongsTo(Dispatcher::class)->select('id', 'name', 'phone', 'email', 'address');
  }

  public function password_resets()
  {
    return $this->morphMany(PasswordReset::class, 'resetable');
  }

  public function prepared_orders()
  {
    return $this->hasMany(Order::class)->where('dispatcher_id', $this->dispatcher_id)->where('chef_id', $this->id)->whereIn('status', ['prepare_completed', 'completed', 'dispatched']);
  }

  public function in_kitchen_orders()
  {
    return $this->hasMany(Order::class)->where('dispatcher_id', $this->dispatcher_id)->where('chef_id', $this->id)->whereIn('status', 'in_kitchen');
  }

  public function almost_ready_orders()
  {
    return $this->hasMany(Order::class)->where('dispatcher_id', $this->dispatcher_id)->where('chef_id', $this->id)->where('status', 'almost_ready');
  }

  public function open_orders()
  {
    return $this->hasMany(Order::class)->where('dispatcher_id', $this->dispatcher_id)->where('status', 'confirmed');
  }


  public function getAvatarAttribute($value)
  {
    return $value == null ? null : asset('images/chefs/' . $value);
  }
}
