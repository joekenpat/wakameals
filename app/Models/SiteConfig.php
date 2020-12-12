<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
  protected $fillable = [
    'key', 'value',
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
    'requested_at' => 'datetime',
  ];

  protected $hidden = ['deleted_at'];

  protected $array_values = ['home_slider'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function getValueAttribute($value)
  {
    if (in_array($this->key, $this->array_values)) {
      return json_decode($value);
    } else {
      return $value;
    }
  }

  public function setValueAttribute($value)
  {
    if (in_array($this->attributes['key'], $this->array_values)) {
      $this->attributes['value'] = json_encode($value);
    } else {
      $this->attributes['value'] = $value;
    }
  }
}
