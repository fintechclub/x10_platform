<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{

    /**
     * Settings
     */
    public function getSettings()
    {

        return view('user.settings');

    }

    /**
     * Save personal settings
     */
    public function postSavePersonal(Request $request)
    {

        \Auth::user()->updatePersonalSettings($request);

        return response()->json([
            'status' => 'success'
        ]);

    }

    /**
     * Check current password
     */
    public function postCheckPassword(Request $request)
    {

        $credentials = [
            'email' => \Auth::user()->email,
            'password' => $request->password
        ];

        return response()->json([
            'status' => \Auth::attempt($credentials) ? 'success' : 'fail'
        ]);

    }

    /**
     * Update user password
     */
    public function postSavePassword(Request $request)
    {

        $user = \Auth::user();

        $user->password = \Hash::make($request->password);

        return response()->json([
            'status' => $user->save() ? 'success' : 'fail'
        ]);

    }

}
