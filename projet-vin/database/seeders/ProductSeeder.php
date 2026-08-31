<?php

namespace Database\Seeders;

use App\Enums\StockUnit;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\StockMovementService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(StockMovementService $stockMovementService): void
    {
        $admin = User::query()
            ->where('email', 'admin@winestock.local')
            ->firstOrFail();

        $categories = Category::query()
            ->whereIn('name', ['Vin rouge', 'Vin blanc', 'Vin rosé', 'Champagne', 'Spiritueux'])
            ->get()
            ->keyBy('name');

        $products = [
            [
                'category' => 'Vin rouge',
                'name' => 'Château Soleil',
                'brand' => 'Domaine du Soleil',
                'vintage' => 2022,
                'purchase_price' => 5500,
                'selling_price' => 8000,
                'barcode' => '3760000000011',
                'stock' => 24,
            ],
            [
                'category' => 'Vin blanc',
                'name' => 'Blanc des Lagunes',
                'brand' => 'Domaine des Lagunes',
                'vintage' => 2023,
                'purchase_price' => 4500,
                'selling_price' => 7000,
                'barcode' => '3760000000028',
                'stock' => 18,
            ],
            [
                'category' => 'Vin rosé',
                'name' => 'Rosé des Sables',
                'brand' => 'Maison des Sables',
                'vintage' => 2024,
                'purchase_price' => 4000,
                'selling_price' => 6500,
                'barcode' => '3760000000035',
                'stock' => 6,
            ],
            [
                'category' => 'Champagne',
                'name' => 'Brut Réserve',
                'brand' => 'Maison Élégance',
                'vintage' => null,
                'purchase_price' => 18000,
                'selling_price' => 25000,
                'barcode' => '3760000000042',
                'stock' => 12,
            ],
            [
                'category' => 'Spiritueux',
                'name' => 'Cognac VSOP',
                'brand' => 'Maison Héritage',
                'vintage' => null,
                'purchase_price' => 22000,
                'selling_price' => 32000,
                'barcode' => '3760000000059',
                'stock' => 0,
            ],
        ];

        foreach ($products as $data) {
            $category = $categories->firstWhere('name', $data['category']);

            if ($category === null) {
                throw new \LogicException("Catégorie introuvable : {$data['category']}");
            }

            $product = Product::query()->firstOrCreate(
                ['barcode' => $data['barcode']],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'brand' => $data['brand'],
                    'vintage' => $data['vintage'],
                    'volume' => '75 cl',
                    'purchase_price' => $data['purchase_price'],
                    'selling_price' => $data['selling_price'],
                    'bottles_per_case' => 6,
                    'minimum_stock' => 6,
                    'is_active' => true,
                ],
            );

            if ($product->wasRecentlyCreated && $data['stock'] > 0) {
                $stockMovementService->recordEntry(
                    $product,
                    $admin,
                    $data['stock'],
                    StockUnit::Bottle,
                    'Stock initial de démonstration',
                    (string) $data['purchase_price'],
                );
            }
        }
    }
}
