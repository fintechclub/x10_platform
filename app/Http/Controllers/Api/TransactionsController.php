<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Portfolio;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{

    /**
     * Create a new transaction
     */
    public function postCreate(Request $request)
    {

        $user = User::find($request->user_id);
        $portfolio = Portfolio::find($request->portfolio_id);

        // create new transaction for specified portfolio
        $tr = $portfolio->transactions()->updateOrCreate(
            [
                'id' => $request->id
            ],
            [
                'asset_id' => $request->asset_id,
                'type' => $request->type,
                'portfolio_id' => $request->portfolio_id,
                'amount' => $request->amount,
                'price_btc' => $request->price_btc,
                'price_usd' => $request->price_usd,
                'comment' => $request->comment,
                'when' => $request->when
            ]);

        // return this transaction
        $tr = Transaction::where('id', '=', $tr->id)->with(['asset'])->first();
        return $tr;

    }

    /**
     * Get transactions
     */
    public function getHistory(Portfolio $portfolio)
    {

        $transactions = Transaction::where('portfolio_id', '=', $portfolio->id)
            ->with('asset')
            ->orderBy('when', 'desc')
            ->get();
        return $transactions;

    }

    /**
     * Delete transaction
     */
    public function postDelete(Request $request)
    {

        $tr = Transaction::find($request->id);

        if ($tr) {
            $tr->delete();
        }

        return response()->json('ok');

    }
}
