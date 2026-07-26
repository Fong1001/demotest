<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\Price;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;

// The default currency
$currency = Currency::where('code', 'USD')->first();

$dummies = [
    [
        'sku' => 'LVL-BEAM-001',
        'name' => 'Premium LVL Timber Beam',
        'description' => 'High strength Laminated Veneer Lumber (LVL) ideal for structural applications.',
        'price' => 15000,
    ],
    [
        'sku' => 'PALLET-001',
        'name' => 'Heavy Duty Solid Wood Pallet',
        'description' => 'Industrial grade solid wood pallet designed for heavy loads and export.',
        'price' => 4500,
    ],
    [
        'sku' => 'FINGER-JOINT-001',
        'name' => 'Precision Finger Joint',
        'description' => 'Flawlessly engineered finger joint timber for seamless construction.',
        'price' => 1250,
    ]
];

foreach ($dummies as $d) {
    if (!ProductVariant::where('sku', $d['sku'])->exists()) {
        $product = Product::create([
            'product_type_id' => 1,
            'status' => 'published',
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\TranslatedText(collect(['en' => $d['name']])),
                'description' => new \Lunar\FieldTypes\TranslatedText(collect(['en' => $d['description']])),
            ]),
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'tax_class_id' => TaxClass::first()->id ?? 1,
            'sku' => $d['sku'],
            'stock' => 100,
            'purchasable' => 'always',
        ]);

        Price::create([
            'priceable_type' => ProductVariant::morphName(),
            'priceable_id' => $variant->id,
            'currency_id' => $currency->id,
            'price' => $d['price'],
        ]);
        
        echo "Created product {$d['sku']}\n";
    } else {
        echo "Product {$d['sku']} already exists\n";
    }
}
