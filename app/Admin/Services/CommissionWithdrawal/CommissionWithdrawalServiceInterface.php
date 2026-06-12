<?php

namespace App\Admin\Services\CommissionWithdrawal;

use Illuminate\Http\Request;

interface CommissionWithdrawalServiceInterface
{

    public function store(Request $request);


    public function update(Request $request);


    public function delete($id);
}
