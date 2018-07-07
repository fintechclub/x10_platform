<?php

namespace App\Http\Controllers\Api;

use App\ExchangeRate;
use App\Http\Controllers\Controller;
use App\Portfolio;
use App\Snapshot;
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

        return [
            'items' => $items,
            'stats' => $portfolio->getCurrentState()['stats'],
            'current_date' => $portfolio->getCurrentState()['current_date'],
            'rates' => [
                'btc_usd' => ExchangeRate::where('title', '=', 'btc_usd')->first()->price,
                'btc_rub' => ExchangeRate::where('title', '=', 'btc_rub')->first()->price
            ],
            'snapshot' => $portfolio->getCurrentState()['snapshot'],
        ];

    }

    /**
     * Create new snapshot and return current portfolio data
     */
    public function getUpdate(Portfolio $portfolio)
    {

        // reload rates for usd,btc, rub
        $url = 'https://api.coingecko.com/api/v3/exchange_rates';
        $data = json_decode(file_get_contents($url));

        $usd = ExchangeRate::where('title', '=', 'btc_usd')->first();
        $usd->price = $data->rates->usd->value;
        $usd->save();

        $rub = ExchangeRate::where('title', '=', 'btc_rub')->first();
        $rub->price = $data->rates->rub->value;
        $rub->save();

        // update rates for portfolio
        $rates = [
            'btc_usd' => ExchangeRate::where('title', '=', 'btc_usd')->first()->price,
            'btc_rub' => ExchangeRate::where('title', '=', 'btc_rub')->first()->price
        ];

        // create snapshot
        $portfolio->generateSnapshot($rates);

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

}
