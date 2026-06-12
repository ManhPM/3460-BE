<?php

namespace App\Admin\Http\Controllers\Home;

use App\Admin\Http\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(){
        return to_route('admin.dashboard');
    }
}
