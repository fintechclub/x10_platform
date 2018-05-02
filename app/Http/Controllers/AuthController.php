<?php

namespace App\Http\Controllers;

use App\Mail\PasswordRecovery;
use App\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

use Cache;
use Validator;

class AuthController extends Controller
{

    /**
     * Show auth page
     */
    public function getIndex($id = null, $token = null)
    {

        if (Auth::check()) {
            return redirect('/dashboard');
        }

        // check if id and activate sent
        if ($id && $token) {

            // check if such user exists
            $user = User::where('id', '=', $id)->where('token', '=', $token)->first();

            // if not, it means that link expired => abort with 404
            if (!$user) {
                abort(404);
            }

        }

        $data = [
            'id' => $id,
            'token' => $token,
            'screen' => $token ? 'activate' : 'login'
        ];

        return view('layouts.auth', $data);

    }


    /**
     * Check auth
     */
    public function postCheckCredentials(Request $request)
    {

        $email = trim(strtolower($request->email));
        $password = $request->password;

        if (!$email || !$password) {

            return response()->json([
                'status' => 'failed',
                'type' => 'Empty fields'
            ]);

        }

        /** @todo: move to system settings */
        $maxTrials = 3;
        $cacheKey = 'auth:' . $email;
        $cacheTrialExpired = 15;

        // limit auth trials
        if (Cache::get($cacheKey) > 3) {

            return response()->json([
                'status' => 'failed',
                'msg' => 'Too many attempts. Wait for 15 minutes.'
            ]);

        }

        // check the username/password
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if (Auth::attempt($credentials)) {

            $user = User::where('email', '=', $email)->first();

            // login user
            Auth::login($user);

            return response()->json([
                'status' => 'success',
                'msg' => ''
            ]);

        } else {

            // save failed trial in cache for a while
            if (Cache::has($cacheKey)) {
                $trials = Cache::get($cacheKey);
            } else {
                $trials = 0;
            }

            $trials++;
            Cache::put($cacheKey, $trials, $cacheTrialExpired);

            // return failed by default
            return response()->json([
                'status' => 'failed',
                'msg' => 'Wrong username/password'
            ]);

        }


    }

    /**
     * Send restore password email
     */
    public function postRestorePasswordRequest(Request $request)
    {

        $email = trim(strtolower($request->email));

        // check user with such email address
        $user = User::where('email', '=', $email)->first();

        if ($user) {

            // set token and send mail
            $token = str_random(64);
            $user->token = $token;
            $user->save();

            // send email
            \Mail::to($user)->send(new PasswordRecovery($user));

        }

        return response()->json([
            'status' => 'success',
            'msg' => ''
        ]);

    }

    /**
     * Activate new password
     */
    public function postActivatePassword(Request $request)
    {

        $token = $request->token;
        $password = $request->password;
        $confirm = $request->confirm;

        // check that they are the same
        if ($password !== $confirm && strlen($password) < 8) {

            return response()->json([
                'status' => 'failed',
                'msg' => 'Passwords are not the same.'
            ]);

        }

        $user = User::where('token', '=', $token)->where('id', '=', $request->id)->first();

        if ($user) {

            $user->password = \Hash::make($password);
            $user->token = '';
            $user->save();

            // login user
            Auth::login($user);

            return response()->json([
                'status' => 'success',
                'msg' => ''
            ]);

        }

        return response()->json([
            'status' => 'failed',
            'msg' => 'Something went wrong'
        ]);

    }
}
