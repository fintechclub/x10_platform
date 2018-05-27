<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{

    /**
     * Reload rate
     */
    public function reloadRate()
    {

        $url = 'https://api.coingecko.com/api/v3/coins/' . $this->coin_id;
        $data = file_get_contents($url);
        $coin = json_decode($data);

        // create new asset price row
        $rate = new AssetRate();

        $rate->asset_id = $this->id;
        $rate->btc = @$coin->market_data->current_price->btc;
        $rate->usd = @$coin->market_data->current_price->usd;
        $rate->rub = @$coin->market_data->current_price->rub;

        $rate->save();

    }

    /**
     * Get tickers select
     */
    public static function tickers()
    {

        $tickers = Asset::orderBy('ticker', 'asc')->get();

        $options = [];

        foreach ($tickers as $ticker) {

            $options[] = [
                'label' => $ticker->ticker,
                'value' => $ticker->id
            ];

        }

        return $options;

    }

}
