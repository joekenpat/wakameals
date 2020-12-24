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
    'delivery_type',
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

  protected $appends = ['total'];

  /**
   * The relationships that are appendable.
   *
   * @var array
   */

  protected $with = [
    'ordered_meals',
    'state',
    'lga',
    'town',
  ];


  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'dispatcher_id',
    'deleted_at',
    'updated_at',
    'state_id',
    'lga_id',
    'town_id',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */

  protected $casts = [];

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

  public function ordered_meals()
  {
    return $this->hasMany(OrderedMeal::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class)->select('id', 'first_name', 'last_name', 'phone', 'title', 'email');
  }

  public function getTotalAttribute()
  {
    $total = 0;
    $ordered_meals = OrderedMeal::whereOrderId($this->id)->get();
    foreach ($ordered_meals as $meal) {
      $total += $meal->cost;
    }
    return $total;
  }

  public function gen_dispatch_code()
  {
    $code = $this->gen_short_code(5);
    if (!static::where('id', '!=', $this->id)->whereDispatchCode($code)->withTrashed()->exists()) {
      $this->dispatch_code = $code;
    } else {
      $code = $this->gen_short_code(6);
      while (static::where('id', '!=', $this->id)->whereDispatchCode($code)->withTrashed()->exists()) {
        $code = $this->gen_short_code(6);
      }
      $this->dispatch_code = $code;
    }
    $this->update();
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
