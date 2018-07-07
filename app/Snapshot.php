<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Snapshot extends Model
{

    protected $casts = [
        'assets' => 'array',
        'stats' => 'array',
        'rates' => 'array',
        'profit' => 'array',
    ];

    /**
     * Portoflio owner
     */
    public function owner()
    {

        return $this->belongsTo('App\User');

    }

    /**
     * Assets from history
     */
    public function assets()
    {
        return $this->hasMany('App\SnapshotAsset')->with('asset');
    }

    /**
     * Portfolio
     */
    public function portfolio()
    {
        return $this->belongsTo('App\Portfolio');
    }

}
