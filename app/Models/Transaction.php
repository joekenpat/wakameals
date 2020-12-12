<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Dyrynda\Database\Casts\EfficientUuid;

class Transaction extends Model
{

  use GeneratesUuid;

  public function uuidColumn(): string
  {
    return 'id';
  }

  public function uuidColumns(): array
  {
    return ['id', 'user_id', 'order_id'];
  }

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'order_id',
    'total_amount',
    'status',
    'gateway',
    'reference',
  ];


  /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';


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

  protected $casts = [
    'id' => EfficientUuid::class,
    'user_id' => EfficientUuid::class,
    'order_id' => EfficientUuid::class,
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function order()
  {
    return $this->belongsTo(Order::class);
  }
}
