<?php

namespace App\Exports;

use App\Models\ProductSubcategory;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StoreProductSubcategoryExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        return ProductSubcategory::with('category')
            ->whereNull('store_id')
            ->orWhere('store_id', $storeId)
            ->get();
    }

    public function map($subcategory): array
    {
        return [
            $subcategory->category->name ?? 'N/A', // Parent Category Name
            $subcategory->name,
            $subcategory->code,
        ];
    }

    public function headings(): array
    {
        return ['Category Name', 'Subcategory Name', 'Code'];
    }
}