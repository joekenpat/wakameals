<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Dyrynda\Database\Casts\EfficientUuid;

class Order extends Model
{

  use GeneratesUuid;

  public function uuidColumn(): string
  {
    return 'id';
  }

  public function uuidColumns(): array
  {
    return ['id', 'user_id'];
  }

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'state_id',
    'lga_id',
    'town_id',
    'address',
    'status',
    'delivery_code',
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

  protected $casts = [
    'id' => EfficientUuid::class,
    'user_id' => EfficientUuid::class,
  ];

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
}
