<?php

namespace App\Models;

use App\Traits\ShortCode;
use App\Traits\UuidForKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

  use UuidForKey, ShortCode, SoftDeletes;


  protected $shortCodeConfig = [
    [
      'length' => 6,
      'column_name' => 'dispatcher_code',
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
    'state_id',
    'lga_id',
    'town_id',
    'address',
    'status',
    'dispatch_code',
    'dispatcher_id',
  ];

  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';

  /**
   * The attributes that are appendable.
   *
   * @var array
   */

  protected $appends = [
    'total'
  ];


  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */

  protected $casts = [];

  public function ordered_meals()
  {
    return $this->hasMany(OrderedMeal::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function getTotalAttribute(): int
  {
    $cost = 0;
    foreach ($this->ordered_meals() as $meals) {
      $cost += $meals->sub_total;
    }
    return $cost;
  }


  /**
   * The "booted" method of this model.
   *
   * @return void
   */
  protected static function booted()
  {
    static::creating(function ($order) {
      $code = $order->gen_short_code(8);
      if (!static::where('id', '!=', $order->id)->whereCode($code)->withTrashed()->exists()) {
        $order->code = $code;
      } else {
        $code = $order->gen_short_code(8);
        while (static::where('id', '!=', $order->id)->whereCode($code)->withTrashed()->exists()) {
          $code = $order->gen_short_code(8);
        }
        $order->code = $code;
      }
    });
  }
}
