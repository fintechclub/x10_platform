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

        $apiUrls = [
            'coingecko' => 'https://api.coingecko.com/api/v3/coins/' . $this->coingecko_id,
            'coinmarket' => 'https://api.coinmarketcap.com/v2/ticker/' . $this->coinmarket_id . '/?convert=btc',
        ];

        // source of data
        $source = '';

        // check if there is correct result for this coin
        $btcPrice = 0;
        $usdPrice = 0;
        $rubPrice = 0;

        foreach ($apiUrls as $key => $url) {

            $data = @file_get_contents($url);

            if ($data === false) {
                continue;
            }

            $coin = json_decode($data);
            $source = $key;


            if ($key == 'coingecko') {


                if (@$coin->market_data) {

                    $btcPrice = @$coin->market_data->current_price->btc;
                    $usdPrice = @$coin->market_data->current_price->usd;
                    $rubPrice = @$coin->market_data->current_price->rub;

                    break;

                }

            }

            if ($key == 'coinmarket') {

                if ($coin->data) {

                    $btcPrice = @$coin->data->quotes->BTC->price;
                    $usdPrice = @$coin->data->quotes->USD->price;

                    // get RUB price
                    $rubUrl = 'https://api.coinmarketcap.com/v2/ticker/' . $this->coinmarket_id . '/?convert=rub';
                    $data = file_get_contents($rubUrl);
                    $coin = json_decode($data);

                    if ($coin->data) {
                        $rubPrice = @$coin->data->quotes->RUB->price;
                    }

                    break;
                }

            }

            if ($btcPrice == 0) {
                return false;
            }

        }

        // create new asset price row
        $rate = new AssetRate();

        $rate->asset_id = $this->id;
        $rate->btc = $btcPrice;
        $rate->usd = $usdPrice;
        $rate->rub = $rubPrice;
        $rate->source = $source;

        $rate->save();

        return $rate;

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
                'label' => $ticker->ticker . '(' . $ticker->title . ')',
                'value' => $ticker->id
            ];

        }

        return $options;

    }

    /**
     * Current rate for this asset
     */
    public function getRate()
    {

        $rate = AssetRate::where('asset_id', '=', $this->id)->orderBy('created_at', 'desc')->first();

        if (!$rate) {

            //update it
            return $this->reloadRate();

        }

        return $rate;

    }
}
