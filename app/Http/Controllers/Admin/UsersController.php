<?php

namespace App\Http\Controllers\Admin;

use App\Asset;
use App\Http\Controllers\Controller;
use App\Portfolio;
use App\Transaction;
use App\User;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UsersController extends Controller
{

    use SEOTools;

    /**
     * Viw user
     */
    public function getView(User $user)
    {

        $this->seo()->setTitle('Пользователи и портфели');

        $data['user'] = $user;

        return view('admin.users.view', $data);

    }

    /**
     * Import portfolio
     */
    public function postImportPortfolio(Request $request)
    {

        $user = User::find($request->user_id);

        $file = $request->file('file');
        $portfolio = Portfolio::find($request->portfolio_id);

        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        foreach ($data as $key => $row) {

            // pass first line
            if ($key == 0) continue;

            // parse data and add transaction
            $tr = new Transaction();

            $date = \DateTime::createFromFormat('d-m-Y', $row['0']);

            $tr->when = $date;

            // find asset
            $asset = Asset::where('ticker', '=', $row[1])->first();
            if ($asset) {
                $tr->asset_id = $asset->id;
            } else {

                $tr->asset_id = '';
                // throw error that such coin doesn't exist
                echo 'ERROR: Such ticker ' . $row[1] . ' not found in system';
                die;
            }

            $tr->amount = doubleval(str_replace('-', '', $row['3']));
            $tr->price_btc = doubleval($row['4']);
            $tr->price_usd = $row['5'] ? doubleval($row[5]) : 0;
            $tr->type = strtolower($row['6']);
            $tr->comment = $row[8];

            $tr->portfolio_id = $portfolio->id;
            $tr->save();

            // handle transaction with Portfolio

            $portfolio->processTransaction($tr);

        }

        // update indexes
        $portfolio->recountIndexes();

        return back();


    }

    /**
     * Clear portfolio
     */
    public function getClearPortfolio(Portfolio $portfolio)
    {

        $portfolio->clear();
        return back();

    }

    /**
     * Create new user
     */
    public function postCreateUser(Request $request)
    {

        $user = new User();
        $user->name = $request->name;
        $user->sname = $request->sname;
        $user->phone = $request->phone;
        $user->email = $request->email;

        // generate password123qweASD
        $pwd = 'x10FUND10x';

        $user->password = bcrypt($pwd);
        $user->tmp_pwd = $pwd;

        $user->save();

        return back();

    }

}
