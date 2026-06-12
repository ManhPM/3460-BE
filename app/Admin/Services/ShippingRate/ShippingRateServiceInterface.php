<?php

namespace App\Admin\Services\ShippingRate;

use Illuminate\Http\Request;

interface ShippingRateServiceInterface
{

    public function store(Request $request);


    public function update(Request $request);


    public function delete($id);
}
