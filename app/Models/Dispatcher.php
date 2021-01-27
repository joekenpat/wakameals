<?php

namespace App\Models;

use App\Traits\ShortCode;
use App\Traits\UuidForKey;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Dispatcher extends Authenticatable
{
  use Notifiable, HasApiTokens, ShortCode, UuidForKey, SoftDeletes;


  protected $shortCodeConfig = [
    [
      'length' => 6,
      'column_name' => 'code',
      'unique' => true
    ]
  ];

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
    'code',
    'name',
    'email',
    'password',
    'place_id',
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
    'deliveries',
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

  public function password_resets()
  {
    $this->morphMany(PasswordReset::class, 'resetable');
  }

  public function deliveries()
  {
    return $this->hasMany(Order::class)->whereIn('status', ['dispatched', 'completed']);
  }


  public function getAvatarAttribute($value): string
  {
    return $value == null ? null : asset('images/dispatchers/' . $value);
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
