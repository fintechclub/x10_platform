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
        'comment',
        'source_id',
        'closed',
        'deduct_btc'
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

    /**
     * Sell transactions
     */
    public function childs()
    {
        return $this->hasMany('App\Transaction', 'source_id');
    }

    /**
     * Calc the amount for that transaction
     */
    public function getAmount()
    {

        $amount = $this->amount;

        // go throught all SELL transactions and deduct it
        foreach ($this->childs as $child) {
            $amount -= $child->amount;
        }

        return $amount;

    }

    /**
     * Close position
     */
    public function checkAndClose()
    {

        if ($this->getAmount() == 0) {

            $this->closed = 1;
            $this->save();

        }

    }
}
