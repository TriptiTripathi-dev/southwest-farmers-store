<?php

namespace App\Exports\Samples;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StoreProductSampleExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'product_name',
            'upc',
            'barcode',
            'sku',
            'unit',
            'cost_price',
            'selling_price',
            'stock_quantity'
        ];
    }

    public function array(): array
    {
        return [
            [
                'Fresh Apple',
                '123456789012',
                'APP-001-BAR',
                'SKU-APP-001',
                'kg',
                '2.50',
                '3.99',
                '100'
            ],
            [
                'Organic Milk',
                '123456789013',
                'MILK-777-BAR',
                'SKU-MILK-777',
                'bottle',
                '1.20',
                '1.85',
                '50'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
