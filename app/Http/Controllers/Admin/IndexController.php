<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IndexController extends Controller
{

    /**
     * Main admin page
     */
    public function getIndex()
    {

        $data['users'] = User::get();

        return view('admin.index', $data);

    }

}
