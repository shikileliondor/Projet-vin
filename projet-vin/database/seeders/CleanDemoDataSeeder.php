<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $barcodes = [
                '3760000000011',
                '3760000000028',
                '3760000000035',
                '3760000000042',
                '3760000000059',
            ];

            $productIds = Product::query()
                ->whereIn('barcode', $barcodes)
                ->pluck('id');

            StockMovement::query()
                ->whereIn('product_id', $productIds)
                ->delete();

            Product::query()
                ->whereIn('id', $productIds)
                ->delete();

            Category::query()
                ->whereIn('name', [
                    'Vin rouge',
                    'Vin blanc',
                    'Vin rose',
                    'Vin rosé',
                    'Champagne',
                    'Spiritueux',
                ])
                ->doesntHave('products')
                ->delete();
        });
    }
}
