<?php

namespace App\Admin\Services\Voucher;

use Illuminate\Http\Request;

interface VoucherServiceInterface
{

    public function store(Request $request);


    public function update(Request $request);


    public function delete($id);
}
