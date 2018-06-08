<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PortfolioAsset extends Model
{

    protected $fillable = [
        'portfolio_id',
        'asset_id',
        'amount'
    ];

    /**
     * Portoflio
     */
    public function portfolio()
    {
        return $this->belongsTo('App\Portfolio');
    }

    /**
     * Asset
     */
    public function asset()
    {
        return $this->belongsTo('App\Asset');
    }

    /**
     * Calc the average BUY price
     */
    public function updateWeightedAvgPrices()
    {

        // operations for weighted average calculation
        $ops = ['sell', 'buy'];

        foreach ($ops as $op) {

            //get all transactions for this portfolio and this asset
            $trs = Transaction::where('portfolio_id', '=', $this->portfolio->id)
                ->where('type', '=', $op)
                ->where('asset_id', '=', $this->asset->id);

            // get total amount of items
            $totalAmount = $trs->sum('amount');

            // get transactions
            $items = $trs->get();

            // calc the prices
            $this->updatePrices($totalAmount, $items, $op);

        }



    }

    /**
     * Calc the prices
     */
    public function updatePrices($totalAmount, $items, $type)
    {

        $avg = [];

        // calc share of assets per each transaction
        foreach ($items as $t) {

            $avg[$t->id] = $t->amount / $totalAmount;

        }

        //calc the price
        $avgPriceBtc = 0;
        $avgPriceUsd = 0;

        foreach ($items as $t) {
            $avgPriceBtc += $t->price_btc * $avg[$t->id];
            $avgPriceUsd += $t->price_usd * $avg[$t->id];
        }

        // update BUY prices
        if ($type == 'buy') {

            // update it
            $this->avg_buy_price_btc = $avgPriceBtc;
            $this->avg_buy_price_usd = $avgPriceUsd;

        }

        // update SELL prices
        if ($type == 'sell') {

            // update it
            $this->avg_sell_price_btc = $avgPriceBtc;
            $this->avg_sell_price_usd = $avgPriceUsd;

        }

        $this->save();

    }

}
