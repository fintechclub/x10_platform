<?php

namespace App\Http\Controllers;

use App\Portfolio;
use App\PortfolioAsset;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Auth;

class DashboardController extends Controller
{

    use SEOTools;

    /**
     * Dashboard main page
     */
    public function getIndex()
    {

        $user = Auth::user();

        $this->seo()->setTitle('Dashboard');

        $total = count(\Auth::user()->portfolios);

        if ($total == 0) {
            return view('dashboard.noitems');
        }

        if ($total == 1) {

            $data['p'] = $p = Portfolio::where('user_id', '=', $user->id)->with(['assets'])->first();

            $assetsInBtc = $p->getAssetsInBtc();


            $data['labels'] = array_keys($assetsInBtc);
            $data['chartData'] = array_values($assetsInBtc);

            return view('dashboard.one-item', $data);

        }

        $data = [];

        $data['total'] = $total;
        $data['portfolios'] = \Auth::user()->portfolios;
        $data['userData'] = Auth::user()->getTotalData();

        return view('dashboard.index', $data);

    }

    /**
     * Show portoflio dashboar
     */
    public function getView(Portfolio $portfolio)
    {

        $data['p'] = $p = $portfolio;

        $this->seo()->setTitle('Dashboard #' . $p->id);

        $assetsInBtc = $p->getAssetsInBtc();

        $data['labels'] = array_keys($assetsInBtc);
        $data['chartData'] = array_values($assetsInBtc);

        return view('dashboard.one-item', $data);

    }
}
