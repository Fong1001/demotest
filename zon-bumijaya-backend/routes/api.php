<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Lunar\Models\Product;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// A simple public API for the Next.js storefront to fetch products
Route::get('/products', function () {
    $products = Product::with(['variants.prices', 'media'])->get();

    $formatted = $products->map(function ($product) {
        $variant = $product->variants->first();
        $price = $variant ? $variant->prices->first() : null;
        $media = $product->media->first();

        // Accessing TranslatedText fields cleanly
        $nameField = $product->translateAttribute('name');
        $descField = $product->translateAttribute('description');

        $name = is_string($nameField) ? $nameField : (is_array($nameField) ? ($nameField['en'] ?? 'Unknown') : ($nameField->value ?? 'Unknown'));
        $description = is_string($descField) ? $descField : (is_array($descField) ? ($descField['en'] ?? '') : ($descField->value ?? ''));

        return [
            'id' => $product->id,
            'slug' => 'product-' . $product->id, // In a real app we would use Lunar's Url model
            'title' => $name,
            'desc' => strip_tags($description),
            'price' => $price ? $price->price->decimal : 0.00,
            'currency' => $price ? $price->currency->code : 'USD',
            'img' => $media ? $media->getUrl() : null,
        ];
    });

    return response()->json([
        'data' => $formatted
    ]);
});
