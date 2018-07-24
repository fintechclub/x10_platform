<?php

namespace App\Console\Commands;

use App\Asset;
use App\AssetRate;
use Illuminate\Console\Command;

class LoadCoinPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_coin_prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $total = 0;

        $bar = $this->output->createProgressBar(10);

        for ($j = 0; $j < 10; $j++) {

            $url = 'https://api.coingecko.com/api/v3/coins?order=gecko_desc%20&per_page=500&page=' . $j;

            $data = file_get_contents($url);
            $json = json_decode($data);

            // save to assets
            foreach ($json as $coin) {

                $asset = Asset::where('ticker', '=', strtoupper($coin->symbol))->first();

                if ($asset) {

                    // create new asset price row
                    $rate = new AssetRate();

                    $rate->asset_id = $asset->id;
                    if ($asset->ticker == 'BTC') {
                        $rate->btc = 1;
                    } else {
                        $rate->btc = @$coin->market_data->current_price->btc;
                    }

                    $rate->usd = @$coin->market_data->current_price->usd;
                    $rate->rub = @$coin->market_data->current_price->rub;

                    $rate->save();

                    $total++;

                }

            }

            $bar->advance();

        }

        $bar->finish();
        echo 'Done ' . $total . PHP_EOL;

    }

}
