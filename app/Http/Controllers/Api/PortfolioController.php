<?php

namespace App\Http\Controllers\Api;

use App\ExchangeRate;
use App\Http\Controllers\Controller;
use App\Portfolio;
use App\Snapshot;
use App\SystemEvent;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{

    /**
     * Create new portfolio for customer
     */
    public function postCreatePortfolio(Request $request)
    {

        $user = User::find($request->customer_id);
        $user->portfolios()->create();

        return back();

    }

    /**
     * Get current portfolio state
     */
    public function getCurrentState(Portfolio $portfolio)
    {

        $items = $portfolio->assets;

        // get rates
        $rates = [
            'btc_usd' => ExchangeRate::where('title', '=', 'btc_usd')->orderBy('created_at', 'desc')->first()->price,
            'btc_rub' => ExchangeRate::where('title', '=', 'btc_rub')->orderBy('created_at', 'desc')->first()->price,
        ];

        return [
            'items' => $items,
            'rates' => $rates
        ];

    }

    /**
     * Create new snapshot and return current portfolio data
     */
    public function getUpdate(Portfolio $portfolio)
    {

        if (env('APP_DEBUG') == true) {

            // save to log what's going on
            $log = new SystemEvent();
            $log->title = 'Update request for portfolio #' . $portfolio->id;
            $log->save();

        }

        // update data and save snapshot
        $portfolio->recountIndexes();

        // and save snapshot
        $portfolio->createSnapshot();

        // return new current state
        return $portfolio->snapshots;

    }

    /**
     * Get snapshots
     */
    public function getSnapshots(Portfolio $portfolio)
    {
        return $portfolio->snapshots;
    }

    /**
     * Get charts
     */
    public function getCharts(Portfolio $portfolio, $type)
    {

        $labels = [];
        $dataUsd = [];
        $dataBtc = [];

        $snapshots = Snapshot::where('portfolio_id', $portfolio->id)->orderBy('created_at', 'asc')->get();

        foreach ($snapshots as $s) {

            $labels[] = $s->created_at->format('Y-m-d');
            $dataUsd[] = $s->usd;
            $dataBtc[] = $s->btc;

        }

        return response()->json([
            'labels' => $labels,
            'usd' => $dataUsd,
            'btc' => $dataBtc
        ]);

    }

    /**
     * Save portfolio data
     */
    public function postSave(Request $request)
    {

        $p = Portfolio::find($request->id);
        $p->updateDeposit($request->deposit);

        return response()->json($p);

    }
}
