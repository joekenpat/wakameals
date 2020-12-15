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

  /**
   * The "booted" method of this model.
   *
   * @return void
   */
  protected static function booted()
  {
    static::created(function ($dispatcher) {
      $code = $dispatcher->gen_short_code(6);
      if (!static::where('id', '!=', $dispatcher->id)->whereCode($code)->withTrashed()->exists()) {
        $dispatcher->code = $code;
      } else {
        $code = $dispatcher->gen_short_code(6);
        while (static::where('id', '!=', $dispatcher->id)->whereCode($code)->withTrashed()->exists()) {
          $code = $dispatcher->gen_short_code(6);
        }
        $dispatcher->code = $code;
      }
    });
  }

  public function getAvatarAttribute($value): string
  {
    return $value == null ? null : public_path('images/dispatchers') . $value;
  }
}
