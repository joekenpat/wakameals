<?php

namespace App\Models;

use App\Traits\ShortCode;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
  use ShortCode;
  protected $fillable = [
    'resetable_id', 'resetable_type', 'code', 'used', 'expires_at'
  ];

  protected $shortCodeConfig = [
    [
      'length' => 6,
      'column_name' => 'code',
      'unique' => true
    ]
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
    'expires_at' => 'datetime',
    'used' => 'boolean',
  ];

  public function resetable()
  {
    return $this->morphTo();
  }


  /**
   * The "booted" method of this model.
   *
   * @return void
   */
  protected static function booted()
  {
    static::creating(function (PasswordReset $password_reset) {
      $password_reset->AddShortCode();
    });
  }
}
