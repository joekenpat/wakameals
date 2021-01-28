<?php

namespace App\Models;

use App\Traits\ShortCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableReservation extends Model
{
  use ShortCode, SoftDeletes;


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
    'code',
    'user_id',
    'place_id',
    'reserved_at',
    'status',
    'seat_quantity',
    'dispatcher_id',
  ];

  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';


  /**
   * The relationships that are appendable.
   *
   * @var array
   */

  protected $with = [
    'place',
    'dispatcher',
    'user',
  ];


  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */

  protected $casts = [
    'reserved_at'
  ];

  public function place()
  {
    return $this->belongsTo(Place::class, 'place_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class)->select('id', 'first_name', 'last_name', 'phone', 'title', 'email');
  }

  public function dispatcher()
  {
    return $this->belongsTo(Dispatcher::class)->select('id', 'name', 'phone', 'email');
  }

  /**
   * The "booted" method of this model.
   *
   * @return void
   */
  protected static function booted()
  {
    static::creating(function ($order) {
      $code = $order->gen_short_code(6);
      if (!static::where('id', '!=', $order->id)->whereCode($code)->withTrashed()->exists()) {
        $order->code = $code;
      } else {
        $code = $order->gen_short_code(6);
        while (static::where('id', '!=', $order->id)->whereCode($code)->withTrashed()->exists()) {
          $code = $order->gen_short_code(6);
        }
        $order->code = $code;
      }
    });
  }
}
