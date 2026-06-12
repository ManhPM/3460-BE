<?php

namespace App\Admin\Services\WalletTransaction;

use Illuminate\Http\Request;

interface WalletTransactionServiceInterface
{
    public function store(Request $request);
    public function update(Request $request);
    public function delete($id);
    public function approve(int $id);
    public function reject(int $id);
}


