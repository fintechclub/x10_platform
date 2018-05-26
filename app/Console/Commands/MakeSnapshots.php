<?php

namespace App\Console\Commands;

use App\ExchangeRate;
use App\Portfolio;
use Illuminate\Console\Command;

class MakeSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make_snapshots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates a snapshots for all portfolios in system';

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

        // total items processed
        $total = 0;

        // chunk size
        $chunk = 500;

        // get current exchange rates btc/usd, btc/rub
        $rates = [
            'btc_usd' => ExchangeRate::where('title', '=', 'btc_usd')->first()->price,
            'btc_rub' => ExchangeRate::where('title', '=', 'btc_rub')->first()->price
        ];

        // get portfolios, chunk it to avoid huge memory consumption
        Portfolio::chunk($chunk, function ($ps) use (&$total, $rates) {

            // loop and create a new snapshot per each one
            /** @var Portfolio $p */
            foreach ($ps as $p) {

                // create a snapshot and save it to database
                $p->generateSnapshot($rates);
                $total++;

            }

        });

        echo 'Done. Total snapshots created: ' . $total;

    }
}
