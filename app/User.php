<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Portfolios
     */
    public function portfolios()
    {
        return $this->hasMany('App\Portfolio');
    }


    /**
     * Update personal settings
     */
    public function updatePersonalSettings($request)
    {

        $this->name = $request->name;
        $this->sname = $request->sname;
        $this->date_birth = $request->date_birth;
        $this->phone = $request->phone;
        $this->email = $request->email;

        $this->save();

    }
}
