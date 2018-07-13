<?php

namespace App;

use Carbon\Carbon;
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
        return $this->hasMany('App\PortfolioAsset')->with(['asset'])->orderBy('asset_id', 'asc');
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
    public function generateSnapshot2()
    {

        // get rates
        $rates = [
            'btc_usd' => ExchangeRate::where('title', '=', 'btc_usd')->orderBy('created_at', 'desc')->first()->price,
            'btc_rub' => ExchangeRate::where('title', '=', 'btc_rub')->orderBy('created_at', 'desc')->first()->price,
        ];

        $snapshot = new Snapshot();

        // go throught each asset and create a summary
        $btc = 0;
        $usd = 0;
        $rub = 0;

        // calc the total
        foreach ($this->assets as $item) {

            $rate = $item->asset->getRate();

            if ($rate) {

                $btc += $item->amount * @$rate->btc;
                $usd += $item->amount * @$rate->usd;
                $rub += $item->amount * @$rate->rub;

            }

        }

        // calc changes from the start
        $changeFromStart = $this->getChangeFromTheFirstSnapshot($btc, $usd);

        // save to snapshot
        $snapshot->portfolio_id = $this->id;

        // current balance
        $snapshot->btc = $btc;
        $snapshot->usd = $usd;
        $snapshot->rub = $rub;

        // save currencies
        $snapshot->btc_usd = @$rates['btc_usd'];
        $snapshot->btc_rub = @$rates['btc_rub'];

        // calc the changes from the start
        $snapshot->btc_from_start = @$changeFromStart['btc'];
        $snapshot->usd_from_start = @$changeFromStart['usd'];

        $snapshot->save();

        // recount current state
        $this->recountIndexes();

        return $snapshot;

    }

    /**
     * Calc the changes from the first snapshot
     */
    public function getChangeFromTheFirstSnapshot($btc, $usd)
    {

        // get first snapshot
        $snapshot = Snapshot::where('portfolio_id', '=', $this->id)
            ->where('btc', '>', 0)
            ->orderBy('created_at', 'asc')->first();

        if ($snapshot) {

            if ($btc > 0 && $usd > 0) {

                return [
                    'btc' => ($btc - $snapshot->btc) / $btc * 100,
                    'usd' => ($usd - $snapshot->usd) / $usd * 100
                ];

            }

        }

        return [
            'btc' => 0,
            'usd' => 0
        ];


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
            'current_date' => $snapshot->created_at,
            'snapshot' => $snapshot
        ];

    }

    /**
     * Get balance usd
     */
    public function getBalanceUsd()
    {

        $val = $this->getCurrentState()['snapshot']->usd;

        return number_format($val, 2);

    }

    /**
     * Get last change
     */
    public function getLastChange()
    {

        return 0;

    }

    /**
     * Get current balance
     */
    public function getBalance($currency = 'btc', $formatted = false)
    {

        $btc = $this->getTotalBtcBalance();

        // current rate
        $rub = ExchangeRate::where('title', '=', 'btc_rub')->orderBy('created_at', 'desc')->first();
        $usd = ExchangeRate::where('title', '=', 'btc_usd')->orderBy('created_at', 'desc')->first();

        if ($currency == 'rub') {

            $res = $btc * $rub->price;

            if ($formatted) {
                return number_format($res, 2);
            }

            return $res;

        }

        if ($currency == 'usd') {

            $res = $btc * $usd->price;

            if ($formatted) {
                return number_format($res, 2);
            }

            return $res;

        }

        if ($formatted) {
            return number_format($btc, 5);
        }

        return $btc;

    }


    /**
     * Get current balance
     */
    public function getTotalBtcBalance()
    {

        $btc = 0;

        foreach ($this->assets as $item) {

            $rate = $item->asset->getRate();

            if ($rate) {
                $btc += $item->amount * $rate->btc;
            } else {

                // throw notification to admin

            }

        }

        return $btc;

    }

    /**
     * Get total growth
     */
    public function getTotalGrowth()
    {
        return 0;
    }

    /**
     * Get total profit
     */
    public function getTotalProfit()
    {

        $bn = $this->getInitialBalance();
        $bt = $this->getBalance('rub');

        if ($bn == 0) {
            return -1;
        }

        $profit = ($bt / $bn - 1) * 100;
        return number_format($profit, 2);
    }

    /**
     * Get life time
     */
    public function getLifeTime()
    {

        $now = Carbon::now();
        return $this->created_at->diffInDays($now);

    }

    /**
     * Get assets in bitcoins
     */
    public function getAssetsInBtc()
    {

        $totalBtc = $this->balance['btc'];
        $assets = [];

        /** @var Asset $a */
        foreach ($this->assets as $a) {

            $rate = $a->asset->getRate();

            if (!$rate) {
                continue;
            }

            $share = number_format($a->amount * $rate->btc / $totalBtc * 100, 2);

            if ($share > env('min_amount', 0.000001)) {
                $assets[$a->asset->title] = $share;
            }

        }

        return $assets;

    }

    /**
     * Get initial balance
     */
    public function getInitialBalance()
    {
        return $this->deposit;
    }

    /**
     * Update portfolio deposit
     */
    public function updateDeposit($deposit)
    {

        $this->deposit = $deposit;
        $this->save();

    }

    /**
     * Recount total balance, growth, etc for current portfolio
     */
    public function recountIndexes()
    {

        // total profit
        $profit = 0;

        $bn = $this->getInitialBalance();
        $bt = $this->getBalance('rub');

        if ($bn == 0) {
            $profit = 0;
        } else {
            $profit = ($bt / $bn - 1) * 100;
        }

        $this->profit = $profit;


        // total growth
        $growth = 0;

        $this->growth = $growth;

        // update balance
        $this->balance = [
            'rub' => $this->getBalance('rub'),
            'usd' => $this->getBalance('usd'),
            'btc' => $this->getBalance(),
        ];


        $this->save();

    }

    /**
     * Save snapshot for current state
     */
    public function createSnapshot()
    {

        // get rates
        $rates = [
            'btc_usd' => ExchangeRate::where('title', '=', 'btc_usd')->orderBy('created_at', 'desc')->first()->price,
            'btc_rub' => ExchangeRate::where('title', '=', 'btc_rub')->orderBy('created_at', 'desc')->first()->price,
        ];

        $snapshot = new Snapshot();

        $btc = $this->balance['btc'];
        $usd = $this->balance['usd'];
        $rub = $this->balance['rub'];

        // calc changes from the start
        $changeFromStart = $this->getChangeFromTheFirstSnapshot($btc, $usd);

        // save to snapshot
        $snapshot->portfolio_id = $this->id;

        // current balance
        $snapshot->btc = $btc;
        $snapshot->usd = $usd;
        $snapshot->rub = $rub;

        // save currencies
        $snapshot->btc_usd = @$rates['btc_usd'];
        $snapshot->btc_rub = @$rates['btc_rub'];

        // calc the changes from the start
        $snapshot->btc_from_start = @$changeFromStart['btc'];
        $snapshot->usd_from_start = @$changeFromStart['usd'];

        $snapshot->save();

        // save snapshot assets history data
        foreach ($this->assets as $asset) {

            $snapshotAsset = new SnapshotAsset();
            $snapshotAsset->snapshot_id = $snapshot->id;
            $snapshotAsset->asset_id = $asset->id;
            $snapshotAsset->amount = $asset->amount;
            $snapshotAsset->rate = $asset->asset->getRate();

            $snapshotAsset->save();

        }


        return $snapshot;

    }


}
