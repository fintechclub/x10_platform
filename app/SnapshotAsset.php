<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SnapshotAsset extends Model
{

    protected $casts = [
        'rate' => 'array',
    ];


    /**
     * Asset
     */
    public function asset()
    {

        return $this->belongsTo('App\Asset');

    }

    /**
     * Portfolio
     */
    public function snapshot()
    {

        return $this->belongsTo('App\Snapshot');

    }


}
