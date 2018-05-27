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

    /* User */
    Route::get('/user/settings', 'UserController@getSettings');
    Route::post('/user/settings/personal/save', 'UserController@postSavePersonal');

    /* Security section*/
    Route::post('/user/settings/security/check-password', 'UserController@postCheckPassword');
    Route::post('/user/settings/security/save-password', 'UserController@postSavePassword');

    /* Portfolio */
    Route::get('/portfolio/{portfolio?}', 'PortfolioController@getIndex');

    /**/
    Route::get('/users/{user}/portfolio/{portfolio}', 'PortfolioController@getView');

});

/**
 * Admin page
 */
Route::group(['middleware' => ['auth']], function () {

    Route::get('/admin/', 'Admin\IndexController@getIndex');
    Route::get('/admin/users/{user}', 'Admin\UsersController@getView');

});

Route::group(['middleware' => ['auth']], function () {



    /* Transactions */
    Route::post('/api/transactions/add', 'Api\TransactionsController@postCreate');
    Route::get('/api/transactions/get/{portfolio}', 'Api\TransactionsController@getHistory');
    Route::post('/api/transactions/delete', 'Api\TransactionsController@postDelete');

    /* Portfolio */
    Route::post('/api/portfolio/create', 'Api\PortfolioController@postCreatePortfolio');
    // get current state with assets/shares/etc
    Route::get('/api/portfolio/current/{portfolio}', 'Api\PortfolioController@getCurrentState');
    Route::get('/api/portfolio/update/{portfolio}', 'Api\PortfolioController@getUpdate');
    Route::get('/api/portfolio/snapshots/{portfolio}', 'Api\PortfolioController@getSnapshots');
});