<?php

namespace App\Exports;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StoreProductCategoryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Export ALL categories visible to this store (Global + Local)
        return ProductCategory::whereNull('store_id')
            ->orWhere('store_id', $storeId)
            ->select('name', 'code') // Sirf ye columns
            ->get();
    }

    public function headings(): array
    {
        return ['Name', 'Code'];
    }
}