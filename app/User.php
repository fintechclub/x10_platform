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

    /**
     * Get total deposit
     */
    public function getTotalData()
    {

        $deposit = 0;
        $profit = 0;
        $growth = 0;

        $totalBalance = 0;

        /** @var Portfolio $p */
        foreach ($this->portfolios as $p) {

            $deposit += $p->deposit;

            // profit
            $totalBalance += $p->balance['rub'];

            // growth
            $growth += $p->growth;

        }

        $profit = ($totalBalance / $deposit - 1) * 100;
        $growth = ($totalBalance - $deposit);

        return [
            'deposit' => $deposit,
            'profit' => $profit,
            'growth' => $growth
        ];

    }
}
