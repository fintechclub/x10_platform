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

        // deduct from btc account
        if ($useInteralBtcAccount == 1) {

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
        if ($useInteralBtcAccount == 1) {
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

            if ($updateBtcAccount == 1) {
                $btcTotal = $amount * $priceBtc;
                $this->updateBtcAccount($btcTotal, 'add');
            }

        }

        if ($type == 'sell') {

            $pAsset->amount += $amount;

            if ($updateBtcAccount == 1) {
                $btcTotal = $amount * $priceBtc;
                $this->updateBtcAccount($btcTotal, 'sub');
            }

        }

        // recount average prices
        $pAsset->updateWeightedAvgPrices();

    }

}
