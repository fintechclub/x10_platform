<?php

namespace App\Http\Controllers\Api;

use App\Asset;
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

        // if it's update, rollback previous transaction and then update
        if ($request->id) {
            $tr = Transaction::find($request->id);
            $portfolio->rollbackTransaction($tr);
        }

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
                'when' => $request->when,
                'deduct_btc' => $request->deduct_btc,
                'source_id' => $request->source_id
            ]);

        // add it to portfolio
        $portfolio->processTransaction($tr);

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
            ->orderBy('id', 'desc')
            ->withTrashed()
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

            // delete transaction from portfolio assets
            $tr->portfolio->rollbackTransaction($tr);

        }

        return response()->json('ok');

    }

    /**
     * Get opened transactions for specified asset
     */
    public function getOpened(Asset $asset, Portfolio $portfolio)
    {

        // get transactions with BUY type and which still opened
        // flag closed appers when we sell all items
        // when it happens, we set closed=1 to initial transaction

        $trs = Transaction::where('asset_id', '=', $asset->id)
            ->where('portfolio_id', '=', $portfolio->id)
            ->where('type', '=', 'buy')
            ->where('closed', '=', null)
            ->get();

        return $trs;

    }
}
