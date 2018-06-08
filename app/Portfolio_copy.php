<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portfolio_copy extends Model
{

    protected $casts = [
        'balance' => 'array',
    ];

    /**
     * Portoflio owner
     */
    public function owner()
    {

        return $this->belongsTo('App\User');

    }

    /**
     * Transactions
     */
    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    /**
     * Snapshots
     */
    public function snapshots()
    {
        return $this->hasMany('App\Snapshot')->orderBy('created_at', 'desc');
    }

    /**
     * Get current balance
     */
    public function getBalance($currency)
    {

        return $this->balance[$currency];

    }

    /**
     * Get stat
     */
    public function getStat()
    {

        return 200;

    }

    /**
     * Get current state with assets
     */
    public function getCurrentState()
    {

        // return the last snapshot
        // get last snapshot
        $snapshot = Snapshot::where('portfolio_id', '=', $this->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$snapshot) {
            return [];
        }

        $items = $snapshot->assets;

        return [
            'items' => $items,
            'stats' => $snapshot->stats,
            'rates' => $snapshot->rates,
            'current_date' => $snapshot->created_at
        ];

    }

    /**
     * Generate a snapshot for current date
     * this function called by Command each day
     *
     * @param mixed $rates Array with current exchange rates
     */
    public function generateSnapshot($rates)
    {

        $buyBtc = Transaction::where('asset_id', '=', 1)
            ->where('type', '=', 'buy')
            ->where('portfolio_id', '=', $this->id)
            ->sum('amount');

        $sellBtc = Transaction::where('asset_id', '=', 1)
            ->where('type', '=', 'sell')
            ->where('portfolio_id', '=', $this->id)
            ->sum('amount');

        $totalBtc = $buyBtc - $sellBtc;

        $snapshot = new Snapshot();
        $snapshot->save();


        // get all transactions and calc it
        $trs = $this->transactions;
        $assets = [];

        foreach ($trs as $t) {

            if (!array_key_exists($t->asset_id, $assets)) {
                $assets[$t->asset_id] = 0;
            }

            // calc the amount
            if ($t->type == 'buy') {

                $assets[$t->asset_id] += $t->amount;

                // reduce btc
                if ($t->deduct_btc === 1) {
                    $totalBtc -= $t->amount * $t->price_btc;
                }

            }

            if ($t->type == 'sell') {
                $assets[$t->asset_id] -= $t->amount;

                // add btc back to btc
                if ($t->deduct_btc === 1) {
                    $totalBtc += $t->amount * $t->price_btc;
                }
            }

        }

        // calc the prices of each one with currency exch rate
        foreach ($assets as $key => $amount) {

            // get current rate for this asset from db
            $rate = AssetRate::where('asset_id', '=', $key)->orderBy('created_at', 'desc')->first();
            $rate_btc = $rate->btc;

            $btc_price = $rate_btc * $amount;

            $prices = [
                'btc' => $btc_price,
                'usd' => $btc_price * $rates['btc_usd'],
                'rub' => $btc_price * $rates['btc_rub']
            ];

            // create new snapshot row
            $asset = new SnapshotAsset();

            $asset->asset_id = $key;
            $asset->amount = $amount;
            $asset->snapshot_id = $snapshot->id;
            $asset->rate_btc = $rate_btc;
            $asset->btc = $prices['btc'];
            $asset->usd = $prices['usd'];
            $asset->rub = $prices['rub'];

            $asset->save();

        }


        // go throught each asset and create a summary
        $btc = 0;
        $usd = 0;
        $rub = 0;

        foreach ($snapshot->assets as $item) {
            $btc += $item->btc;
            $usd += $item->usd;
            $rub += $item->rub;
        }

        $stats = [
            'total_btc' => $totalBtc,
            'balance_btc' => $btc,
            'balance_usd' => $usd,
            'balance_rub' => $rub
        ];

        // calc the profit
        $profit = [
            'usd' => '',
            'btc' => ''
        ];

        // save to snapshot

        $snapshot->portfolio_id = $this->id;
        $snapshot->stats = $stats;
        $snapshot->rates = $rates;
        $snapshot->profit = $profit;

        $snapshot->save();

    }

    /**
     * Update assets rates for this portfolio
     */
    public function updateAssetsRates()
    {

        // get ids of assets in portfolio
        $ids = array_keys($this->calcAssets());

        // reload the rate per each one
        $assets = Asset::whereIn('id', $ids)->get();

        /** @var Asset $a */
        foreach ($assets as $a) {
            $a->reloadRate();
        }

    }

    /**
     * Calculate assets in portfolio, transaction based
     */
    public function calcAssets()
    {

        $trs = $this->transactions;
        $assets = [];

        foreach ($trs as $t) {

            if (!array_key_exists($t->asset_id, $assets)) {
                $assets[$t->asset_id] = 0;
            }

            // calc the amount
            if ($t->type == 'buy') {

                $assets[$t->asset_id] += $t->amount;

                // reduce btc

            }

            if ($t->type == 'sell') {
                $assets[$t->asset_id] -= $t->amount;

                // add btc back to btc heap

            }

        }

        return $assets;

    }

}
