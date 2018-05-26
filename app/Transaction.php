<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = [
        'when',
        'asset_id',
        'portfolio_id',
        'type',
        'price_btc',
        'price_usd',
        'amount',
        'comment'
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
    public function portfolio()
    {

        return $this->belongsTo('App\Portfolio');

    }

    /**
     * Operation
     */
    public function operation()
    {
        return $this->belongsTo('App\Operation');
    }


}
