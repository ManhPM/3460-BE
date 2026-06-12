<?php

namespace App\Admin\Services\Bank;

use Illuminate\Http\Request;

interface BankServiceInterface
{
    public function update(Request $request);
    public function store(Request $request);
}
