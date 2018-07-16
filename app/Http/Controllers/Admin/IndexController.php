<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IndexController extends Controller
{

    use SEOTools;

    /**
     * Main admin page
     */
    public function getIndex()
    {

        $this->seo()->setTitle('Пользователи и портфели');

        $data['users'] = User::get();

        return view('admin.index', $data);

    }

    /**
     * Import users and portfolios
     */
    public function getView()
    {



    }


}
