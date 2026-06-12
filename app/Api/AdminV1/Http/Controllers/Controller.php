<?php

namespace App\Api\AdminV1\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ApiResponse;

    protected $repository;
    protected $service;

}

