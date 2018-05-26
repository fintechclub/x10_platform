<?php

namespace App\Console\Commands;

use App\Asset;
use Illuminate\Console\Command;

class LoadCoins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load_coins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load coins from coingecko';

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

        $url = 'https://api.coingecko.com/api/v3/coins/list';

        $data = file_get_contents($url);
        $json = json_decode($data);

        // save to assets
        foreach ($json as $coin) {

            $asset = Asset::where('ticker', '=', strtoupper($coin->symbol))->first();

            if (!$asset) {

                $asset = new Asset();

                $asset->ticker = strtoupper($coin->symbol);
                $asset->title = $coin->name;

                $asset->save();

            }

            if ($asset && !$asset->coin_id) {

                $asset->coin_id = $coin->id;
                $asset->save();

            }


        }

    }

}
