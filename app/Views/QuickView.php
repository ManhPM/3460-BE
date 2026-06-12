<?php

namespace App\Views;

use Illuminate\View\Component;
use App\Admin\Traits\GetConfig;

class QuickView extends Component
{
    use GetConfig;

    public function __construct() {}

    public function render()
    {
        return view('components.quickview');
    }
}
