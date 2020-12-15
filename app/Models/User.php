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
    'state_id',
    'lga_id',
    'town_id',
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

  public function state()
  {
    return $this->belongsTo(State::class, 'state_id');
  }

  public function lga()
  {
    return $this->belongsTo(Lga::class, 'lga_id');
  }

  public function town()
  {
    return $this->belongsTo(Town::class, 'town_id');
  }

  public function orders()
  {
    return $this->belongsTo(Order::class, 'user_id');
  }

  public function getAvatarAttribute($value): string
  {
    return $value == null ? null : public_path('images/users') . $value;
  }
}
