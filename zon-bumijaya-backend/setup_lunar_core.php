<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Lunar\Facades\DB;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxZone;

DB::transaction(function () {
    if (! Channel::whereDefault(true)->exists()) {
        echo "Setting up default channel\n";
        Channel::create([
            'name' => 'Webstore',
            'handle' => 'webstore',
            'default' => true,
            'url' => 'http://localhost:3000',
        ]);
    }

    if (! Language::count()) {
        echo "Adding default language\n";
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'default' => true,
        ]);
    }

    if (! Currency::whereDefault(true)->exists()) {
        echo "Adding a default currency (USD)\n";
        Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'exchange_rate' => 1,
            'decimal_places' => 2,
            'default' => true,
            'enabled' => true,
        ]);
    }

    if (! CustomerGroup::whereDefault(true)->exists()) {
        echo "Adding a default customer group.\n";
        CustomerGroup::create([
            'name' => 'Retail',
            'handle' => 'retail',
            'default' => true,
        ]);
    }

    if (! CollectionGroup::count()) {
        echo "Adding an initial collection group\n";
        CollectionGroup::create([
            'name' => 'Main',
            'handle' => 'main',
        ]);
    }

    if (! TaxClass::count()) {
        echo "Adding a default tax class.\n";
        TaxClass::create([
            'name' => 'Default Tax Class',
            'default' => true,
        ]);
    }

    if (! TaxZone::count()) {
        echo "Adding a default tax zone.\n";
        $taxZone = TaxZone::create([
            'name' => 'Default Tax Zone',
            'zone_type' => 'country',
            'price_display' => 'tax_exclusive',
            'default' => true,
            'active' => true,
        ]);
        $taxZone->countries()->createMany(
            Country::get()->map(fn ($country) => [
                'country_id' => $country->id,
            ])
        );
    }

    if (! Attribute::count()) {
        echo "Setting up initial attributes\n";
        $group = AttributeGroup::create([
            'attributable_type' => Product::morphName(),
            'name' => collect(['en' => 'Details']),
            'handle' => 'details',
            'position' => 1,
        ]);

        $collectionGroup = AttributeGroup::create([
            'attributable_type' => Collection::morphName(),
            'name' => collect(['en' => 'Details']),
            'handle' => 'collection_details',
            'position' => 1,
        ]);

        Attribute::create([
            'attribute_type' => 'product',
            'attribute_group_id' => $group->id,
            'position' => 1,
            'name' => ['en' => 'Name'],
            'handle' => 'name',
            'section' => 'main',
            'type' => TranslatedText::class,
            'required' => true,
            'default_value' => null,
            'configuration' => ['richtext' => false],
            'system' => true,
            'description' => ['en' => ''],
        ]);

        Attribute::create([
            'attribute_type' => 'collection',
            'attribute_group_id' => $collectionGroup->id,
            'position' => 1,
            'name' => ['en' => 'Name'],
            'handle' => 'name',
            'section' => 'main',
            'type' => TranslatedText::class,
            'required' => true,
            'default_value' => null,
            'configuration' => ['richtext' => false],
            'system' => true,
            'description' => ['en' => ''],
        ]);

        Attribute::create([
            'attribute_type' => 'product',
            'attribute_group_id' => $group->id,
            'position' => 2,
            'name' => ['en' => 'Description'],
            'handle' => 'description',
            'section' => 'main',
            'type' => TranslatedText::class,
            'required' => false,
            'default_value' => null,
            'configuration' => ['richtext' => true],
            'system' => false,
            'description' => ['en' => ''],
        ]);

        Attribute::create([
            'attribute_type' => 'collection',
            'attribute_group_id' => $collectionGroup->id,
            'position' => 2,
            'name' => ['en' => 'Description'],
            'handle' => 'description',
            'section' => 'main',
            'type' => TranslatedText::class,
            'required' => false,
            'default_value' => null,
            'configuration' => ['richtext' => true],
            'system' => false,
            'description' => ['en' => ''],
        ]);
    }

    if (! ProductType::count()) {
        echo "Adding a product type.\n";
        $type = ProductType::create([
            'name' => 'Stock',
        ]);

        $type->mappedAttributes()->attach(
            Attribute::whereAttributeType(
                Product::morphName()
            )->get()->pluck('id')
        );
    }
});
echo "Done setting up Lunar core tables!\n";
