<?php

namespace App\Models;

use App\Traits\ShortCode;
use App\Traits\UuidForKey;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Dispatcher extends Authenticatable
{
  use Notifiable, HasApiTokens, ShortCode, UuidForKey;


  protected $shortCodeConfig = [
    [
      'length' => 6,
      'column_name' => 'code',
      'unique' => true
    ]
  ];

  const filterables = [
    'name', 'state', 'lga', 'blocked',
  ];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'avatar',
    'code',
    'first_name',
    'last_name',
    'email',
    'password',
    'state_id',
    'lga_id',
    'town_id',
    'address',
    'last_ip',
    'last_login',
    'blocked_at',
    'type'
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

  public function password_resets()
  {
    $this->morphMany(PasswordReset::class, 'resetable');
  }

  public function orders()
  {
    return $this->hasMany(Order::class)->whereIn('status', ['dispatched', 'completed']);
  }


  public function getAvatarAttribute($value): string
  {
    return $value == null ? null : public_path('images/dispatchers') . $value;
  }

  /**
   * The "booted" method of this model.
   *
   * @return void
   */
  protected static function booted()
  {
    static::creating(function (Dispatcher $dispatcher) {
      $dispatcher->AddShortCode();
    });
  }
}
