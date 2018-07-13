<?php

namespace App\Http\Controllers;

use App\Portfolio;
use App\User;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{

    use SEOTools;

    /**
     * Portfolio main page
     */
    public function getIndex(Portfolio $portfolio)
    {

        $this->seo()->setTitle('Portfolio');

        $user = Auth::user();

        // if portfolio is here, show portfolio details
        if ($portfolio->id) {

            $this->seo()->setTitle('Просмотр портфеля');

            $data['portfolio'] = $portfolio;
            $data['user'] = $user;

            return view('portfolio.view', $data);

        }


        $data['portfolios'] = $user->portfolios;

        if (count($data['portfolios']) == 1) {

            // redirect to portfolio item
            return redirect('/portfolio/' . $user->portfolios[0]->id);

        }

        return view('portfolio.index', $data);

    }

    /**
     * View portoflio
     */
    public function getView(User $user, Portfolio $portfolio)
    {

        /** @todo: check the rights for access */

        $portfolio->fresh(['assets']);

        $data['portfolio'] = $portfolio;
        $data['user'] = $user;

        return view('portfolio.view', $data);

    }
}
