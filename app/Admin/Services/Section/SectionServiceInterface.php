<?php

namespace App\Admin\Services\Section;

use Illuminate\Http\Request;

interface SectionServiceInterface
{
    /**
     * Tạo mới
     *
     * @return mixed
     * @var Illuminate\Http\Request $request
     *
     */
    public function store(Request $request);

    /**
     * Cập nhật
     *
     * @return boolean
     * @var Illuminate\Http\Request $request
     *
     */
    public function update(Request $request);
    public function delete($id);
}
