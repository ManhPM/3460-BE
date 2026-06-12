<?php

namespace App\Admin\Http\Controllers;

use App\Traits\AdminResponse;

class Controller extends BaseController
{
    use AdminResponse;
    protected $repository;

    protected $service;
}
