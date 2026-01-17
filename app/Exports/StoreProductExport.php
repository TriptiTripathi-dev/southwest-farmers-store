<?php
namespace App\Exports;
use App\Models\StoreStock;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StoreProductExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection() {
        $user = Auth::user();
        return StoreStock::where('store_id', $user->store_id ?? $user->id)
            ->with('product')->get();
    }
    public function map($stock): array {
        return [
            $stock->product->product_name,
            $stock->product->sku,
            $stock->quantity,
            $stock->selling_price,
            $stock->product->store_id ? 'Local' : 'Warehouse'
        ];
    }
    public function headings(): array {
        return ['Name', 'SKU', 'Qty', 'Price', 'Type'];
    }
}