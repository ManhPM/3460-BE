<?php

namespace App\Admin\Services\Product;

use Illuminate\Http\Request;

interface ProductServiceInterface
{
    public function createProductVariations(Request $request, array $view);
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
    /**
     * Xóa
     *  
     * @param int $id
     * 
     * @return boolean
     */
    public function delete($id);

    /**
     * Xóa sạch toàn bộ sản phẩm và bảng liên quan
     * 
     * @return boolean
     */
    public function clearAllData();

    /**
     * Nhập sản phẩm từ file Excel
     * 
     * @param Request $request
     * @return int Số lượng sản phẩm đã nhập thành công
     */
    public function import(Request $request);
}