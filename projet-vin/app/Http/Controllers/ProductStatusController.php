<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductStatusRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class ProductStatusController extends Controller
{
    public function update(UpdateProductStatusRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return back()->with('success', $product->is_active ? 'Produit activé.' : 'Produit désactivé.');
    }
}
