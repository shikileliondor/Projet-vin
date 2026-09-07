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

        $category = Category::query()->firstOrCreate(
            ['name' => 'Vin'],
            ['is_active' => true],
        );

        foreach ($this->products() as $data) {
            $product = Product::query()->firstOrCreate(
                ['barcode' => $data['barcode']],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'brand' => null,
                    'vintage' => $data['vintage'],
                    'volume' => $data['volume'],
                    'purchase_price' => null,
                    'selling_price' => $data['selling_price'],
                    'bottles_per_case' => 1,
                    'minimum_stock' => 1,
                    'is_active' => true,
                ],
            );

            if ($product->wasRecentlyCreated && $data['stock'] > 0) {
                $stockMovementService->recordEntry(
                    $product,
                    $admin,
                    $data['stock'],
                    StockUnit::Bottle,
                    'Stock initial reel',
                    null,
                );
            }
        }
    }

    /**
     * @return list<array{
     *     barcode: string,
     *     name: string,
     *     vintage: int|null,
     *     volume: string,
     *     selling_price: string,
     *     stock: int
     * }>
     */
    private function products(): array
    {
        return [
            [
                'barcode' => '2774663000',
                'name' => 'Grand Sud Merlot 12,5 % vol 3 Liter Bag in Box',
                'vintage' => null,
                'volume' => '3 L',
                'selling_price' => '12.99',
                'stock' => 2,
            ],
            [
                'barcode' => '2259460000',
                'name' => 'JANEE rouge Merlot IGP 13,0 % vol',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '3.49',
                'stock' => 6,
            ],
            [
                'barcode' => '2896592000',
                'name' => 'Bigi Vipra Rosso Umbria IGT 13,5 % vol',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '4.19',
                'stock' => 6,
            ],
            [
                'barcode' => '2598799000',
                'name' => 'Iliada Cabernet Sauvignon Tempranillo Syrah 13,5 % vol',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '4.99',
                'stock' => 6,
            ],
            [
                'barcode' => '2596714000',
                'name' => 'Nerosso Nero di Troia Puglia IGT 13,0 % vol',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '3.99',
                'stock' => 6,
            ],
            [
                'barcode' => '1837076000',
                'name' => 'Baron Philippe de Rothschild Bordeaux Rouge AOC 13,0 % vol',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '5.99',
                'stock' => 6,
            ],
            [
                'barcode' => '2990828000',
                'name' => 'Deutsches Weintor Spatburgunder trocken 13,0 % vol',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '4.99',
                'stock' => 6,
            ],
            [
                'barcode' => '1893395000',
                'name' => 'Vinetti Hugo 6,9 % vol',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '1.79',
                'stock' => 12,
            ],
            [
                'barcode' => '2504361000',
                'name' => 'Vinetti Wildberry Spritz 8,0 % vol',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '1.79',
                'stock' => 12,
            ],
            [
                'barcode' => '100269054',
                'name' => 'Cabernet Merlot Peloponnes PGE trocken',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
            [
                'barcode' => '100061626',
                'name' => 'Cotes du Rhone Villages AOP trocken, Rot',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
            [
                'barcode' => '100229907',
                'name' => 'Spatburgunder Baden QbA trocken, Rotwein',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
            [
                'barcode' => '100180919',
                'name' => 'Alentejo DOC trocken, Rotwein',
                'vintage' => 2024,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
            [
                'barcode' => '100208214',
                'name' => 'Cabernet Sauvignon trocken, Rotwein',
                'vintage' => 2022,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
            [
                'barcode' => '100398479',
                'name' => 'Cuvee Tradition AOC trocken, Rotwein',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
            [
                'barcode' => '100367226',
                'name' => 'Cru Bourgeois AOC Medoc trocken, Rotwein',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 5,
            ],
            [
                'barcode' => '100212594',
                'name' => 'EDITION MILD Grau- und Weissburgunder QbA',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
            [
                'barcode' => '100199560',
                'name' => 'Corbieres AOP trocken, Rotwein',
                'vintage' => 2023,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 11,
            ],
            [
                'barcode' => '100061628',
                'name' => 'Costieres de Nimes AOP trocken, Rotwein',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
            [
                'barcode' => '100360514',
                'name' => 'Barrel Smugglers Cabernet Sauvignon Mend',
                'vintage' => null,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
            [
                'barcode' => '100336224',
                'name' => 'Rioja Reserva DOCa trocken, Rotwein',
                'vintage' => 2019,
                'volume' => '75 cl',
                'selling_price' => '0.00',
                'stock' => 6,
            ],
        ];
    }
}
