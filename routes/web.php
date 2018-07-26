<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {

    // check auth
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect('/dashboard');
    }

    return redirect('/auth');

});


Auth::routes();

Route::group(['middleware' => []], function () {

    /* Auth */
    Route::get('/auth', 'AuthController@getIndex')->name('login');
    Route::get('/auth/logout', function () {
        Auth::logout();
        return redirect('/dashboard');
    });

    Route::get('/auth/activate/{id}/{token}', 'AuthController@getIndex');
    Route::post('auth/check-credentials', 'AuthController@postCheckCredentials');
    Route::post('auth/restore-password', 'AuthController@postRestorePasswordRequest');
    Route::post('auth/activate-password', 'AuthController@postActivatePassword');

});


Route::get('/mailable', function () {

    $user = \App\User::first();

    return new \App\Mail\PasswordRecovery($user);
});


Route::group(['middleware' => ['auth']], function () {

    Route::get('/dashboard', 'DashboardController@getIndex');
    Route::get('/dashboard/{portfolio}', 'DashboardController@getView');

    /* User */
    Route::get('/user/settings', 'UserController@getSettings');
    Route::post('/user/settings/personal/save', 'UserController@postSavePersonal');

    /* Security section*/
    Route::post('/user/settings/security/check-password', 'UserController@postCheckPassword');
    Route::post('/user/settings/security/save-password', 'UserController@postSavePassword');

    /* Portfolio */
    Route::get('/portfolio/{portfolio?}', 'PortfolioController@getIndex');

    /* Faq*/
    Route::get('/faq', 'FaqController@index');

    /**/
    Route::get('/users/{user}/portfolio/{portfolio}', 'PortfolioController@getView');

});

/**
 * Admin page
 */
Route::group(['middleware' => ['auth', 'admin']], function () {

    Route::get('/admin/', 'Admin\IndexController@getIndex');
    Route::get('/admin/users/{user}', 'Admin\UsersController@getView');

    Route::post('/admin/users/import-portfolio', 'Admin\UsersController@postImportPortfolio');
    Route::get('/admin/users/clear-portfolio/{portfolio}', 'Admin\UsersController@getClearPortfolio');
    Route::post('/admin/users/create', 'Admin\UsersController@postCreateUser');
    Route::post('/admin/users/update-portfolio/{p}', 'Admin\UsersController@postUpdatePortfolio');

});

Route::group(['middleware' => ['auth']], function () {


    /* Transactions */
    Route::post('/api/transactions/add', 'Api\TransactionsController@postCreate');
    Route::get('/api/transactions/get/{portfolio}', 'Api\TransactionsController@getHistory');
    Route::post('/api/transactions/delete', 'Api\TransactionsController@postDelete');
    Route::get('/api/transactions/opened/{asset}/{portfolio}', 'Api\TransactionsController@getOpened');

    /* Portfolio */
    Route::post('/api/portfolio/create', 'Api\PortfolioController@postCreatePortfolio');
    // get current state with assets/shares/etc
    Route::get('/api/portfolio/current/{portfolio}', 'Api\PortfolioController@getCurrentState');
    Route::get('/api/portfolio/update/{portfolio}', 'Api\PortfolioController@getUpdate');
    Route::get('/api/portfolio/snapshots/{portfolio}', 'Api\PortfolioController@getSnapshots');
    Route::get('/api/portfolio/charts/{portfolio}/{type}', 'Api\PortfolioController@getCharts');

    // save  portfolio data
    Route::post('/api/portfolio/save', 'Api\PortfolioController@postSave');

    /* Asset */
    Route::get('/api/assets/{asset}/price', 'Api\AssetsController@getPrice');
});

Route::get('/test/{id}', function ($id) {

    $portfolio = \App\Portfolio::find($id);

    $btc = 0.30651601122714;
    $usd = 2165.3671551193;

    dd($portfolio->getChangeFromTheFirstSnapshot($btc, $usd));

});

Route::get('/upgrade-1/{id?}', function ($id = null) {

    if (!$id) {
        $snapshots = \App\Snapshot::all();
    } else {
        $snapshots = \App\Snapshot::where('id', '=', $id)->get();
    }

    foreach ($snapshots as $snapshot) {

        $stat = $snapshot->stats;
        $rates = $snapshot->rates;

        $snapshot->btc = $stat['balance_btc'];
        $snapshot->usd = $stat['balance_usd'];
        $snapshot->rub = $stat['balance_rub'];

        if (is_array($rates) && count($rates) > 0) {
            $snapshot->btc_usd = $rates['btc_usd'];
            $snapshot->btc_rub = $rates['btc_rub'];
        }

        $snapshot->save();

    }

    echo 'Done';

});

// update changes from the start
Route::get('/upgrade-2/{id?}', function ($id = null) {

    if (!$id) {
        $snapshots = \App\Snapshot::all();
    } else {
        $snapshots = \App\Snapshot::where('id', '=', $id)->get();
    }

    foreach ($snapshots as $snapshot) {

        if ($snapshot->portfolio) {

            $changeFromStart = $snapshot->portfolio->getChangeFromTheFirstSnapshot($snapshot->btc, $snapshot->usd);

            $snapshot->btc_from_start = $changeFromStart['btc'];
            $snapshot->usd_from_start = $changeFromStart['usd'];

            $snapshot->save();

        }

    }

    echo 'Done';

});

Route::get('/upgrade-3/{id?}', function ($id = null) {

    if (!$id) {
        $portfolios = \App\Portfolio::all();
    } else {
        $portfolios = \App\Portfolio::where('id', '=', $id)->get();
    }

    foreach ($portfolios as $p) {

        $p->recountIndexes();

    }

    echo 'Done';

});

Route::get('/upgrade-4/{id?}', function ($id = null) {

    if (!$id) {
        $portfolios = \App\Portfolio::all();
    } else {
        $portfolios = \App\Portfolio::where('id', '=', $id)->get();
    }

    foreach ($portfolios as $p) {

        // generate new snapshot
        $p->createSnapshot();

    }

    echo 'Done';

});

Route::get('/update-currencies', function () {

    \App\SystemEvent::updateCurrencies();

    echo 'Updated';

});

// update coin prices
Route::get('/update-coin-prices', function () {

    $code = Artisan::call('update_coin_prices');
    echo 'Done';

});