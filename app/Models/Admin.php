<?php

namespace App\Models;

use App\Traits\UuidForKey;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
  use Notifiable, HasApiTokens, UuidForKey;

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
    'password',
    'place_id',
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
    'email_verified_at' => 'datetime',
    'blocked_at' => 'datetime',
  ];

  public function place()
  {
    return $this->belongsTo(Place::class, 'place_id');
  }

  public function password_resets()
  {
    return $this->morphMany(PasswordReset::class, 'resetable');
  }

  public function getAvatarAttribute($value): string
  {
    return $value == null ? null : asset('images/admins/' . $value);
  }
}
