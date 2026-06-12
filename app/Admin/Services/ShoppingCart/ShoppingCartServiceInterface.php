<?php

namespace App\Admin\Services\ShoppingCart;

use Illuminate\Http\Request;

interface ShoppingCartServiceInterface
{
    /**
     * Tạo mới
     *
     * @var Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function store(Request $request);
    /**
     * Cập nhật
     *
     * @var Illuminate\Http\Request $request
     *
     * @return boolean
     */
    public function update(Request $request);

    public function delete($id);
    public function storeNotLogin(Request $request);
    public function checkout(Request $request);
    public function calculateDiscountValue($total, $discountOrVoucher);
    public function calculateTotal($shoppingCart);
    public function calculateTotalFromSession($cart);
    public function calculateShippingDiscountValue($total, $discountOrVoucher, $shippingFee);
}
