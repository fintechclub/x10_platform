<?php

namespace App\Http\Controllers;

use App\Portfolio;
use App\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{

    /**
     * Portfolio main page
     */
    public function getIndex(Portfolio $portfolio)
    {


        $user = Auth::user();

        // if portfolio is here, show portfolio details
        if($portfolio->id){

            $data['portfolio'] = $portfolio;
            $data['user'] = $user;

            return view('portfolio.view', $data);

        }


        $data['portfolios'] = $user->portfolios;

        return view('portfolio.index', $data);

    }

    /**
     * View portoflio
     */
    public function getView(User $user, Portfolio $portfolio)
    {

        /** @todo: check the rights for access */

        $data['portfolio'] = $portfolio;
        $data['user'] = $user;

        return view('portfolio.view', $data);

    }
}
