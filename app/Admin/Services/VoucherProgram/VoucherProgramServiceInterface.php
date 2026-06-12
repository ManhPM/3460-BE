<?php

namespace App\Admin\Services\VoucherProgram;

use Illuminate\Http\Request;

interface VoucherProgramServiceInterface
{
    public function store(Request $request);
    public function giveVoucher(Request $request);
    public function reset(Request $request);
    public function update(Request $request);
    public function delete($id);
}
