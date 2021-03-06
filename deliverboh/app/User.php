<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'restaurant_name', 'address','vat_number', 'brand', 'restaurant_image'
    ];
    public function dishes(){
        return $this->hasMany('App\Dish');
    }
    // public function restaurant_types(){
    //     return $this->belongsToMany('App\Restaurant_type');
    // }
    public function categories(){
        return $this->belongsToMany('App\Category');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
