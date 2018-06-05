<?php

namespace App\Http\Controllers\Api;

use App\Asset;
use App\Http\Controllers\Controller;
use App\Portfolio;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;

class AssetsController extends Controller
{

    /**
     * Get asset price
     */
    public function getPrice(Asset $asset)
    {

        return $asset->getRate();

    }
}
