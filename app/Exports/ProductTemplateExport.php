<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'Sản phẩm mẫu 1',
                100000,
                80000,
                50,
                'Mô tả sản phẩm mẫu 1'
            ],
            [
                'Sản phẩm mẫu 2',
                150000,
                null,
                100,
                'Mô tả sản phẩm mẫu 2'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Tên sản phẩm',
            'Giá sản phẩm',
            'Giá khuyến mãi',
            'Số lượng',
            'Mô tả'
        ];
    }
}
