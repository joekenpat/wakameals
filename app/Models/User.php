<?php

namespace App\Models;

use App\Traits\UuidForKey;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
  use UuidForKey, Notifiable, HasApiTokens;

  const filterables = [
    'name', 'place', 'blocked',
  ];

  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'avatar',
    'first_name',
    'last_name',
    'email',
    'phone',
    'password',
    'place_id',
    'address',
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
    'place_id',
    'last_ip',
    'blocked_at',
    'updated_at',
    'deleted_at',
    'created_at',
    'email_verified_at'
  ];


  protected $withCount  = [
    'orders',
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

  public function cart_items()
  {
    return $this->hasMany(Cart::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class, 'user_id');
  }

  public function password_resets()
  {
    $this->morphMany(PasswordReset::class, 'resetable');
  }

  public function pending_table_reservations()
  {
    return $this->hasMany(TableReservation::class)->where('status', 'pending');
  }

  public function cancelled_table_reservations()
  {
    return $this->hasMany(TableReservation::class)->where('status', 'cancelled');
  }

  public function approved_table_reservations()
  {
    return $this->hasMany(TableReservation::class)->where('status', 'approved');
  }

  public function getAvatarAttribute($value)
  {
    return $value == null ? null : asset('images/users/' . $value);
  }
}
