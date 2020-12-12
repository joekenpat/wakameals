<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{

    const filterables = [
        'name',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'slug',
    ];

    /**
   * The datetime format for this model.
   *
   * @var String
   */
  protected $dateFormat = 'Y-m-d H:i:s.u';

    public function orders()
    {
        return $this->hasMany(Order::class, 'state_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'state_id');
    }

    public function admins()
    {
        return $this->hasMany(Admin::class, 'state_id');
    }
    public function dispatchers()
    {
        return $this->hasMany(Dispatcher::class, 'state_id');
    }

    public function lgas()
    {
        return $this->hasMany(Lga::class, 'state_id');
    }

    public function towns()
    {
        return $this->hasMany(town::class, 'state_id');
    }

}
