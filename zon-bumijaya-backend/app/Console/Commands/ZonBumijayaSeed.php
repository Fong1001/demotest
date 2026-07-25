<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\Price;
use Lunar\Models\Currency;
use Lunar\Models\TaxClass;
use Lunar\Models\ProductType;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\FieldTypes\Text;

class ZonBumijayaSeed extends Command
{
    protected $signature = 'zon:seed';
    protected $description = 'Seed Zon Bumijaya dummy products into Lunar';

    public function handle()
    {
        $this->info('Starting Zon Bumijaya Seeder for Lunar...');

        $currency = Currency::where('code', 'USD')->first();
        if (!$currency) {
            $this->error('USD currency not found! Run lunar:install first.');
            return;
        }

        $taxClass = TaxClass::first();
        $productType = ProductType::first();
        $collectionGroup = CollectionGroup::first();

        if (!$taxClass || !$productType || !$collectionGroup) {
            $this->error('Missing TaxClass, ProductType, or CollectionGroup. Run lunar:install first.');
            return;
        }

        $collection = Collection::firstOrCreate([
            'collection_group_id' => $collectionGroup->id,
        ], [
            'attribute_data' => collect([
                'name' => new Text('Timber'),
            ]),
        ]);

        $products = [
            [
                'sku' => 'LVL-BEAM-001',
                'name' => 'Premium LVL Timber Beam',
                'price' => 15000,
                'desc' => 'High strength Laminated Veneer Lumber (LVL) ideal for structural applications.',
                'image' => 'https://placehold.co/600x400/e9c176/412d00?text=LVL+Beam'
            ],
            [
                'sku' => 'PALLET-001',
                'name' => 'Heavy Duty Solid Wood Pallet',
                'price' => 4500,
                'desc' => 'Industrial grade solid wood pallet designed for heavy loads and export.',
                'image' => 'https://placehold.co/600x400/e9c176/412d00?text=Solid+Pallet'
            ],
            [
                'sku' => 'FINGER-JOINT-001',
                'name' => 'Precision Finger Joint',
                'price' => 1250,
                'desc' => 'Flawlessly engineered finger joint timber for seamless construction.',
                'image' => 'https://placehold.co/600x400/e9c176/412d00?text=Finger+Joint'
            ]
        ];

        foreach ($products as $data) {
            // Delete if exists
            $existing = ProductVariant::where('sku', $data['sku'])->first();
            if ($existing && $existing->product) {
                $existing->product->delete();
            }

            // Create Product
            $product = Product::create([
                'product_type_id' => $productType->id,
                'status' => 'published',
                'attribute_data' => collect([
                    'name' => new Text($data['name']),
                    'description' => new Text($data['desc']),
                ]),
            ]);

            // Attach to collection
            $product->collections()->attach($collection->id);

            // Create Variant
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'tax_class_id' => $taxClass->id,
                'sku' => $data['sku'],
                'shippable' => true,
            ]);

            // Create Price
            $variant->prices()->create([
                'price' => $data['price'],
                'currency_id' => $currency->id,
                'min_quantity' => 1,
            ]);

            // Add Image (Media Library)
            try {
                $product->addMediaFromUrl($data['image'])->toMediaCollection('images');
            } catch (\Exception $e) {
                $this->warn('Could not attach image for ' . $data['sku'] . ': ' . $e->getMessage());
            }

            $this->info("Created product: " . $data['sku']);
        }
        
        $this->info('Seeding complete!');
    }
}
