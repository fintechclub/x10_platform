<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
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
     * Assets in this portfolio
     */
    public function assets()
    {
        return $this->hasMany('App\PortfolioAsset')->with(['asset']);
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
     * Push asset
     */
    public function getPortfolioAsset(Asset $asset)
    {

        // check if such asset create in this portfolio
        $portfolioAsset = PortfolioAsset::where('portfolio_id', '=', $this->id)
            ->where('asset_id', '=', $asset->id)
            ->first();

        // if such asset not created, add the new one
        if (!$portfolioAsset) {

            $portfolioAsset = new PortfolioAsset();

            $portfolioAsset->asset_id = $asset->id;
            $portfolioAsset->amount = 0;
            $portfolioAsset->portfolio_id = $this->id;

            $portfolioAsset->save();

        }

        return $portfolioAsset;

    }

    /**
     * Buy asset operation
     *
     * @param Asset $asset
     * @param double $amount
     * @param double $priceBtc
     * @param bool $useInteralBtcAccount
     */
    public function buy(Asset $asset, $amount, $priceBtc, $useInteralBtcAccount = 0)
    {

        $portfolioAsset = $this->getPortfolioAsset($asset);

        // add amount
        $portfolioAsset->amount += doubleval($amount);
        $portfolioAsset->save();

        // deduct from btc account only if it's not a btc
        if ($useInteralBtcAccount == 1 && $asset->ticker != 'BTC') {

            $btcTotal = $amount * $priceBtc;
            $this->updateBtcAccount($btcTotal, 'sub');

        }

        // update weihted average prices
        $portfolioAsset->updateWeightedAvgPrices();

    }


    /**
     * Sell asset from portfolio
     * @param Asset $asset
     * @param double $amount
     * @param double $priceBtc
     * @param bool $useInteralBtcAccount
     */
    public function sell(Asset $asset, $amount, $priceBtc, $useInteralBtcAccount = 0)
    {

        $portfolioAsset = $this->getPortfolioAsset($asset);

        // add amount
        $portfolioAsset->amount -= $amount;

        // save
        $portfolioAsset->save();

        // deduct from btc account
        if ($useInteralBtcAccount == 1 && $asset->ticker != 'BTC') {
            $btcTotal = $amount * $priceBtc;
            $this->updateBtcAccount($btcTotal, 'add');
        }

        // update weihted average prices
        $portfolioAsset->updateWeightedAvgPrices();

    }


    /**
     * Withdraw asset
     */
    public function withdraw(Asset $asset, $amount, $price)
    {

        $portfolioAsset = $this->getPortfolioAsset($asset);

        // add amount
        $portfolioAsset->amount -= $amount;

        // save
        $portfolioAsset->save();

        // update prices
        $portfolioAsset->updateWeightedAvgPrices();

    }

    /**
     * Update btc account
     */
    public function updateBtcAccount($btcAmount, $type)
    {

        // get asset for BTC
        $btc = Asset::where('ticker', '=', 'BTC')->first();

        // try to get or create the new row for BTC in portfolio
        $btcAccount = $this->getPortfolioAsset($btc);

        if ($type == 'add') {
            $btcAccount->amount += $btcAmount;
        }

        if ($type == 'sub') {
            $btcAccount->amount -= $btcAmount;
        }

        $btcAccount->save();

    }

    /**
     * Manage portfolio assets from transaction
     */
    public function processTransaction(Transaction $t)
    {

        if ($t->type == 'buy') {
            $this->buy($t->asset, $t->amount, $t->price_btc, $t->deduct_btc);
        }

        if ($t->type == 'sell') {
            $this->sell($t->asset, $t->amount, $t->price_btc, $t->deduct_btc);
        }

        if ($t->type == 'withdraw') {
            $this->withdraw($t->asset, $t->amount, $t->price_btc);
        }

    }

    /**
     * Revert back transaction
     * @param Transaction $tr
     */
    public function rollbackTransaction(Transaction $tr)
    {
        $this->rollback($tr->asset, $tr->amount, $tr->price_btc, $tr->type, $tr->deduct_btc);
    }

    /**
     * Rollback
     * @param Asset $asset
     * @param double $amount
     */
    public function rollback(Asset $asset, $amount, $priceBtc, $type, $updateBtcAccount = 0)
    {

        $pAsset = $this->getPortfolioAsset($asset);

        // if it was buy operation
        // get asset and substract amount
        if ($type == 'buy') {

            $pAsset->amount -= $amount;

            if ($updateBtcAccount == 1 && $asset->ticker != 'BTC') {
                $btcTotal = $amount * $priceBtc;
                $this->updateBtcAccount($btcTotal, 'add');
            }

        }

        if ($type == 'sell') {

            $pAsset->amount += $amount;

            if ($updateBtcAccount == 1 && $asset->ticker != 'BTC') {
                $btcTotal = $amount * $priceBtc;
                $this->updateBtcAccount($btcTotal, 'sub');
            }

        }

        // recount average prices
        $pAsset->updateWeightedAvgPrices();

    }

    /**
     * Generate a snapshot for current date
     * this function called by Command each day
     *
     */
    public function generateSnapshot($rates = [])
    {

        $snapshot = new Snapshot();

        // go throught each asset and create a summary
        $btc = 0;
        $usd = 0;
        $rub = 0;

        // update exchange rates and generate new snapshot

        foreach ($this->assets as $item) {

            $rate = $item->asset->getRate();

            $btc += $item->amount * $rate->btc;
            $usd += $item->amount * $rate->usd;
            $rub += $item->amount * $rate->rub;

        }

        $stats = [
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

        return $snapshot;

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
            $snapshot = $this->generateSnapshot();
        }

        return [
            'stats' => $snapshot->stats,
            'current_date' => $snapshot->created_at
        ];

    }

    /**
     * Get balance usd
     */
    public function getBalanceUsd()
    {

        return $this->getCurrentState()['stats']['balance_usd'];


    }


}
