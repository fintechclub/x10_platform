<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemEvent extends Model
{
    //

    /**
     * Update currencies for calculations
     */
    public static function updateCurrencies()
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

        $log = new SystemEvent();
        $log->title = 'Currencies updated';
        $log->body = json_encode($data);

        $log->save();

    }

}
