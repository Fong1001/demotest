---
name: aimeos-expert
description: Expert skill for Aimeos e-commerce on Laravel. Covers the Manager/Item API, template overrides, JSON API, theming, catalog/product/basket/checkout architecture, and all the hard-won gotchas from real production work. Use this skill whenever working on the Aimeos Laravel backend (zon-bumijaya-backend).
---

# Aimeos Expert Skill — Laravel E-Commerce

> This skill encodes deep knowledge of Aimeos architecture, its Laravel integration, the Manager/Item data layer, template override system, JSON API, theming, and critical gotchas discovered through production work. Every rule below is battle-tested.

---

## 0. ARCHITECTURE OVERVIEW

Aimeos is a **component-based e-commerce framework** that plugs into host frameworks (Laravel, TYPO3, Slim). It is NOT a standalone app — it provides:

1. **Managers** — Data access layer (like repositories). Every domain (product, catalog, order, customer, media, text, price, stock, etc.) has its own Manager.
2. **Items** — Data objects returned by managers. Items are populated via `fromArray()` and read via `get*()` methods or `toArray()`.
3. **Controllers** — Business logic layer (cart operations, checkout flow, etc.)
4. **HTML Client Components** — Server-rendered PHP components (catalog/lists, catalog/detail, basket/standard, checkout/standard, account/history, etc.)
5. **Templates** — PHP view files that render the HTML for each component.
6. **JSON API** — RESTful API following JSON:API spec for headless/decoupled frontends.

### Key Packages
| Package | Purpose |
|---|---|
| `aimeos/aimeos-core` | Core library: managers, items, controllers, setup tasks |
| `aimeos/ai-client-html` | HTML client components + default templates |
| `aimeos/ai-controller-frontend` | Frontend controllers (basket, checkout, catalog) |
| `aimeos/ai-controller-jobs` | Background job controllers (order processing, subscriptions) |
| `aimeos/ai-admin-jqadm` | Admin panel (JQAdm) |
| `aimeos/ai-admin-jsonadm` | Admin JSON API |
| `aimeos/aimeos-laravel` | Laravel integration: routes, service providers, blade views |

---

## 1. LARAVEL INTEGRATION — THE VIEW NAMESPACE TRAP

**This is the #1 gotcha that will waste hours if you don't know it.**

### 1.A How Aimeos Registers Its Views

Aimeos registers its blade templates under the `shop` namespace via the Laravel service provider. The base layout lives at:
```
vendor/aimeos/aimeos-laravel/views/base.blade.php
```

All page-level blade templates (catalog/home, catalog/list, catalog/detail, basket/index, checkout/index, account/index) extend this base:
```php
@extends('shop::base')
```

### 1.B How to Override the Base Layout (CRITICAL)

To override `shop::base`, you MUST place your custom file at:
```
resources/views/vendor/shop/base.blade.php
```

**NOT** `resources/views/vendor/aimeos/base.blade.php` — that path does NOTHING.

Laravel's package view override resolution for a namespace `shop` looks in:
```
resources/views/vendor/shop/
```

### 1.C How to Override Page-Level Templates

The page templates (catalog/home.blade.php, catalog/list.blade.php, etc.) live at:
```
vendor/aimeos/aimeos-laravel/views/catalog/home.blade.php
vendor/aimeos/aimeos-laravel/views/catalog/list.blade.php
vendor/aimeos/aimeos-laravel/views/catalog/detail.blade.php
vendor/aimeos/aimeos-laravel/views/basket/index.blade.php
vendor/aimeos/aimeos-laravel/views/checkout/index.blade.php
vendor/aimeos/aimeos-laravel/views/account/index.blade.php
```

To override, copy to:
```
resources/views/vendor/shop/catalog/home.blade.php
resources/views/vendor/shop/catalog/list.blade.php
... etc.
```

### 1.D Blade Sections Available in base.blade.php

The base layout yields these sections that page templates fill:
```
@yield('aimeos_header')       — CSS/JS head includes from components
@yield('aimeos_head_nav')     — Navigation tree component
@yield('aimeos_head_locale')  — Language/currency selector
@yield('aimeos_head_search')  — Search bar component
@yield('aimeos_head_basket')  — Mini basket component
@yield('aimeos_stage')        — Stage/hero area
@yield('aimeos_body')         — Main content area
@yield('aimeos_scripts')      — Footer JS
@yield('content')             — Standard Laravel content
```

### 1.E The CSS/JS Problem

Aimeos ships TWO CSS files and TWO JS files:
```html
<link href="vendor/shop/themes/default/app.css">     <!-- Bootstrap + layout -->
<link href="vendor/shop/themes/default/aimeos.css">   <!-- Component styles -->
<script src="vendor/shop/themes/default/app.js">      <!-- Bootstrap JS -->
<script src="vendor/shop/themes/default/aimeos.js">   <!-- Component JS (Vue-based) -->
```

**WARNING:** `aimeos.js` is REQUIRED for interactive components (add-to-basket, quantity selectors, variant selection, checkout steps). If you remove it, the store breaks. `aimeos.css` styles the internal component markup. You CAN override it, but you must understand what you're overriding.

**Strategy for custom theming:**
1. Keep `aimeos.css` and `aimeos.js` loaded (they drive component interactivity).
2. Add your own CSS AFTER them to override styles.
3. Use `!important` or higher specificity selectors to win the cascade.
4. Alternatively, create a custom theme (see Section 6).

---

## 2. THE MANAGER / ITEM DATA LAYER

### 2.A Getting a Manager
```php
$context = app('aimeos.context')->get();
$manager = \Aimeos\MShop::create($context, 'product');
```

Available domains: `product`, `catalog`, `order`, `customer`, `media`, `text`, `price`, `stock`, `attribute`, `service`, `supplier`, `coupon`, `plugin`, `tag`, `review`, `rule`, `cms`, `locale`, `locale/site`.

### 2.B Creating Items
```php
$item = $manager->create();

// Set data via fromArray — GOTCHA: requires variable by reference!
$data = [
    'product.code' => 'MY-SKU-001',
    'product.label' => 'My Product',
    'product.type' => 'default',
    'product.status' => 1,
];
$item = $item->fromArray($data);
$manager->save($item);
```

**CRITICAL GOTCHA:** `fromArray()` takes its argument by reference internally. You CANNOT pass an array literal directly:
```php
// THIS WILL FAIL:
$item->fromArray(['product.code' => 'X']);

// THIS WORKS:
$data = ['product.code' => 'X'];
$item->fromArray($data);
```

### 2.C Searching / Filtering Items
```php
$filter = $manager->filter();
$filter->setConditions($filter->compare('==', 'product.code', 'MY-SKU-001'));
$items = $manager->search($filter);
$item = $items->first();
```

Filter operators: `==`, `!=`, `>`, `>=`, `<`, `<=`, `=~` (starts with), `~=` (contains).

Combining conditions:
```php
$filter->setConditions($filter->and([
    $filter->compare('==', 'product.type', 'default'),
    $filter->compare('==', 'product.status', 1),
]));
```

### 2.D List Items (Relationships Between Domains)

Products connect to media, text, price, attributes, categories, etc. via "list items." This is how Aimeos handles relationships.

```php
// Add a text to a product
$textManager = \Aimeos\MShop::create($context, 'text');
$textData = [
    'text.type' => 'long',
    'text.languageid' => 'en',
    'text.content' => 'Full product description here.',
];
$textItem = $textManager->create()->fromArray($textData);
$textManager->save($textItem);

// Link text to product via list item
$listItem = $manager->createListItem();
$listItem->setRefId($textItem->getId());
$manager->save($item->addListItem('text', $listItem, $textItem));
```

Text types: `name` (product name), `short` (short description), `long` (full description), `meta-title`, `meta-description`, `meta-keyword`.

Media types: `default` (product image), `download` (downloadable file).

Price types: `default` (standard price), `purchase` (purchase/cost price).

### 2.E Catalog (Categories) — Nested Set Tree

Catalogs use a **nested set** model (lft/rgt columns). The root category typically has code `home`.

```php
$catalogManager = \Aimeos\MShop::create($context, 'catalog');

// Find root
$filter = $catalogManager->filter();
$filter->setConditions($filter->compare('==', 'catalog.code', 'home'));
$root = $catalogManager->search($filter)->first();

// Get tree
$tree = $catalogManager->getTree($root->getId());

// Add product to catalog
$listItem = $catalogManager->createListItem();
$listItem->setRefId($productItem->getId());
$catalogManager->save($root->addListItem('product', $listItem));
```

**GOTCHA:** The default Aimeos setup creates a root catalog with code `home`, NOT `root`. Always verify with tinker:
```bash
sail artisan tinker --execute="echo \Aimeos\MShop::create(app('aimeos.context')->get(), 'catalog')->search(\Aimeos\MShop::create(app('aimeos.context')->get(), 'catalog')->filter())->first()->getCode();"
```

---

## 3. TEMPLATE OVERRIDE SYSTEM (HTML Components)

### 3.A Two Separate Override Systems

Aimeos has TWO template systems that are often confused:

1. **Blade templates** (Section 1) — The Laravel page-level layouts. Override path: `resources/views/vendor/shop/`.
2. **PHP component templates** — The Aimeos internal component templates. Override path: configured via `shop.php` config.

### 3.B PHP Component Template Overrides

Component templates live in:
```
vendor/aimeos/ai-client-html/templates/client/html/
```

Structure:
```
catalog/
  filter/body.php, header.php
  lists/body.php, header.php, items.php
  detail/body.php, header.php
  home/body.php
  search/body.php
  session/body.php
  stage/body.php
  tree/body.php
basket/
  mini/body.php
  standard/body.php
checkout/
  standard/body.php
  confirm/body.php
account/
  history/body.php
  profile/body.php
  review/body.php
  watch/body.php
  favorite/body.php
  subscription/body.php
locale/
  select/body.php
```

### 3.C How to Override Component Templates

**Method 1: Custom theme directory (RECOMMENDED)**

In `config/shop.php`:
```php
'client' => [
    'html' => [
        'common' => [
            'template' => [
                'baseurl' => 'packages/aimeos/shop/themes/my-theme',
            ],
        ],
    ],
],
```

Then create your override templates at:
```
public/packages/aimeos/shop/themes/my-theme/client/html/catalog/lists/body.php
```

**Method 2: Direct vendor override (QUICK BUT FRAGILE)**

Copy the template directly from vendor and modify in place. This works but will be overwritten on `composer update`.

**Method 3: Config-based template path**

In `config/shop.php`:
```php
'client' => [
    'html' => [
        'catalog' => [
            'lists' => [
                'template-body' => 'catalog/lists/body',
                'template-header' => 'catalog/lists/header',
            ],
        ],
    ],
],
```

### 3.D Template Variables Available

Inside component templates (`body.php`), you have access to:
```php
$this->get('listProductItems')   // Product items in catalog list
$this->get('listCatPath')        // Category breadcrumb path
$this->get('detailProductItem')  // Single product in detail view
$this->get('basketItem')         // Current basket
$this->csrf()->name()            // CSRF token name
$this->csrf()->value()           // CSRF token value
$this->url(...)                  // URL generation
$this->translate(...)            // Translation
$this->config(...)               // Configuration access
```

---

## 4. JSON API (Headless / Decoupled Frontend)

### 4.A Base URL
```
http://localhost:8000/jsonapi
```

### 4.B Key Endpoints

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/jsonapi` | API entry point (discover all endpoints) |
| GET | `/jsonapi/product` | List products |
| GET | `/jsonapi/product/{id}` | Single product |
| GET | `/jsonapi/catalog` | List categories |
| GET | `/jsonapi/catalog/{id}` | Single category with products |
| GET | `/jsonapi/stock` | Product stock levels |
| POST | `/jsonapi/basket` | Create basket |
| GET | `/jsonapi/basket?id=default` | Get current basket |
| POST | `/jsonapi/basket?id=default&related=product` | Add product to basket |
| DELETE | `/jsonapi/basket?id=default&related=product&relatedid={id}` | Remove from basket |
| POST | `/jsonapi/order` | Create order from basket |
| GET | `/jsonapi/order` | List orders (authenticated) |

### 4.C Including Related Resources

Use the `include` parameter to fetch related data in a single request:
```
GET /jsonapi/product?include=media,text,price,attribute
GET /jsonapi/catalog?include=media,text,product
```

The response follows JSON:API spec with `data`, `included`, and `relationships` sections.

### 4.D Product Response Structure
```json
{
  "data": [{
    "id": "1",
    "type": "product",
    "attributes": {
      "product.id": "1",
      "product.code": "MY-SKU",
      "product.label": "Product Name",
      "product.type": "default",
      "product.status": 1
    },
    "relationships": {
      "media": { "data": [{ "id": "5", "type": "media" }] },
      "text": { "data": [{ "id": "10", "type": "text" }] },
      "price": { "data": [{ "id": "3", "type": "price" }] }
    }
  }],
  "included": [
    {
      "id": "5",
      "type": "media",
      "attributes": {
        "media.url": "/path/to/image.jpg",
        "media.preview": "/path/to/thumbnail.jpg"
      }
    }
  ]
}
```

### 4.E CORS for Decoupled Frontends

If your Next.js frontend runs on a different port (e.g., localhost:3000), you need CORS configured in Laravel. Add to `config/cors.php`:
```php
'paths' => ['api/*', 'jsonapi/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000'],
```

---

## 5. SEEDING DATA PROGRAMMATICALLY

### 5.A Custom Artisan Command Pattern

```php
class ZonBumijayaSeed extends Command
{
    protected $signature = 'zon:seed';
    
    public function handle()
    {
        $context = app('aimeos.context')->get();
        $manager = \Aimeos\MShop::create($context, 'product');
        $priceManager = \Aimeos\MShop::create($context, 'price');
        $textManager = \Aimeos\MShop::create($context, 'text');
        $mediaManager = \Aimeos\MShop::create($context, 'media');
        $catalogManager = \Aimeos\MShop::create($context, 'catalog');
        
        // Always find catalog FIRST
        $filter = $catalogManager->filter();
        $filter->setConditions($filter->compare('==', 'catalog.code', 'home'));
        $catalog = $catalogManager->search($filter)->first();
        
        // Create product
        $prodData = ['product.code' => 'SKU-001', 'product.label' => 'My Product', 'product.type' => 'default', 'product.status' => 1];
        $item = $manager->create()->fromArray($prodData);
        $manager->save($item);
        
        // Create and link price
        $priceData = ['price.type' => 'default', 'price.currencyid' => 'USD', 'price.value' => '99.00', 'price.quantity' => 1];
        $priceItem = $priceManager->create()->fromArray($priceData);
        $priceManager->save($priceItem);
        $manager->save($item->addListItem('price', $manager->createListItem()->setRefId($priceItem->getId()), $priceItem));
        
        // Link to catalog
        $catalogManager->save($catalog->addListItem('product', $catalogManager->createListItem()->setRefId($item->getId())));
    }
}
```

### 5.B Resetting Data

```bash
sail artisan migrate:fresh     # Wipe all tables
sail artisan aimeos:setup      # Recreate Aimeos schema + seed demo data
sail artisan zon:seed          # Run custom seeder
```

**WARNING:** `aimeos:setup` will re-seed demo data if `SHOP_DEMO=1` in `.env`. Set `SHOP_DEMO=0` to prevent demo data from being re-created.

---

## 6. THEMING (Creating a Custom Theme)

### 6.A Theme Directory Structure

Create a new theme at:
```
public/vendor/shop/themes/my-theme/
  app.css          — Main stylesheet (overrides default)
  aimeos.css       — Component overrides (optional)
  app.js           — Main JS (optional)
  aimeos.js        — Component JS overrides (optional)
  assets/
    logo.png       — Custom logo
    icon.png       — Favicon
```

### 6.B Activating a Theme

In `config/shop.php` (or admin panel > Settings):
```php
'themes' => ['my-theme'],
```

Or via the admin panel: go to Settings > select your site > set the theme.

### 6.C CSS Variable Overrides

Aimeos uses CSS custom properties. Override them in your theme's `app.css`:
```css
:root {
  --ai-bg: #1a1a2e;
  --ai-bg-alt: #16213e;
  --ai-color: #e0e0e0;
  --ai-color-alt: #a0a0a0;
  --ai-primary: #e9c176;
  --ai-primary-alt: #d4a84e;
  --ai-secondary: #0f3460;
  --ai-danger: #e94560;
}
```

---

## 7. COMMON GOTCHAS & HARD-WON LESSONS

### 7.A Template Override Paths
- Blade layout: `resources/views/vendor/shop/base.blade.php` (namespace = `shop`)
- NOT `resources/views/vendor/aimeos/` — that does nothing
- Component templates: configured in `config/shop.php` or via custom theme

### 7.B fromArray() Reference Trap
Always extract array data into a local variable before passing to `fromArray()`.

### 7.C The Catalog Code
Default root catalog code is `home`, not `root`. Always verify.

### 7.D Demo Data Re-seeding
Set `SHOP_DEMO=0` in `.env` before running `aimeos:setup` to avoid re-creating demo products.

### 7.E aimeos.js Is Required
Do NOT remove `aimeos.js` from the layout. It powers Vue-based component interactivity (add to cart, quantity inputs, variant selectors, checkout flow).

### 7.F CSS Cascade War
Aimeos ships Bootstrap + its own CSS. If you add Tailwind, expect specificity conflicts. Solutions:
1. Load your CSS AFTER Aimeos CSS
2. Use `@layer` to control cascade
3. Use Tailwind's `!important` mode (`important: true` in config)
4. Or strip Aimeos CSS entirely and rebuild from scratch (high effort)

### 7.G The Locale/Search Dropdown Problem
The injected `@yield('aimeos_head_locale')` and `@yield('aimeos_head_search')` components render raw Bootstrap-styled HTML. If your layout uses Tailwind/custom CSS, these components will look broken. Solutions:
1. Remove the `@yield` calls and replace with your own search/locale UI
2. Override the component templates (`locale/select/body.php`, `catalog/search/body.php`)
3. Hide them with CSS (`display: none`) and build custom equivalents

### 7.H Admin Panel Access
```
http://localhost:8000/admin
```
Default credentials: whatever you created with `sail artisan aimeos:account admin@example.com --super`.

### 7.I Index Rebuilding & Seeding Race Conditions
After seeding products, always rebuild the search index:
```bash
sail artisan aimeos:jobs "index/rebuild"
```

**CRITICAL SEEDER GOTCHA:**
If you create products in a Seeder and manually map them to a Category, running `index/rebuild` at the end of your Seeder will **wipe out your custom `mshop_index_catalog` mappings**! 
To fix this, you must:
1. Create the products.
2. Run `$this->call('aimeos:jobs', ['jobs' => ['index/rebuild']]);` (This populates `mshop_index_price` and `mshop_index_text`).
3. **AFTER** the rebuild, run a manual DB insert to restore the `mshop_index_catalog` links:
```php
\Illuminate\Support\Facades\DB::statement(
    'INSERT IGNORE INTO mshop_index_catalog (catid, prodid, listtype, siteid, pos, mtime) VALUES (?, ?, ?, ?, ?, ?)',
    [$catalogId, $productId, 'default', '1.', 0, date('Y-m-d H:i:s')]
);
```

### 7.J The f_catid Routing Trick
By default, Aimeos `catalog/filter` or `catalog/lists` expects a category ID in the URL. If you hit `/shop/search` directly, it may return 0 products because it has no context. To set a default catalog view, redirect your root route to pass the `f_catid` parameter:
```php
Route::get('/', function () {
    return redirect('/shop/search?f_catid=2');
});
```
---

## 8. DEBUGGING RECIPES

### 8.A Check What Products Exist
```bash
sail artisan tinker --execute="
\$ctx = app('aimeos.context')->get();
\$mgr = \Aimeos\MShop::create(\$ctx, 'product');
foreach(\$mgr->search(\$mgr->filter()) as \$item) {
    echo \$item->getCode() . ' — ' . \$item->getLabel() . PHP_EOL;
}
"
```

### 8.B Check Catalog Structure
```bash
sail artisan tinker --execute="
\$ctx = app('aimeos.context')->get();
\$mgr = \Aimeos\MShop::create(\$ctx, 'catalog');
foreach(\$mgr->search(\$mgr->filter()) as \$item) {
    echo \$item->getCode() . ' (ID: ' . \$item->getId() . ')' . PHP_EOL;
}
"
```

### 8.C Test JSON API
```bash
curl http://localhost:8000/jsonapi/product?include=media,text,price
```

### 8.D Check Which Template Is Being Used
Add at the top of any component template:
```php
<?php echo "<!-- TEMPLATE: " . __FILE__ . " -->"; ?>
```

---

## 9. MANDATORY WORKFLOW FOR AIMEOS TASKS

When working on any Aimeos task, follow this checklist:

1. **Identify the layer:** Is this a blade layout issue (Section 1), a component template issue (Section 3), a data issue (Section 2/5), or an API issue (Section 4)?
2. **Check the override path:** Use the CORRECT path for the layer. Double-check namespace.
3. **Test after every change:** Aimeos caches aggressively. Clear caches:
   ```bash
   sail artisan cache:clear
   sail artisan view:clear
   sail artisan config:clear
   ```
4. **Never break aimeos.js:** Keep it loaded unless you're building a fully headless frontend.
5. **Rebuild index after data changes:** Always run `index/rebuild` after seeding.
6. **Verify in browser:** Always hard-refresh (Ctrl+Shift+R) after template changes.
