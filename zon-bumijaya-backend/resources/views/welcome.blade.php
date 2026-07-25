@extends('layouts.storefront')

@section('content')
<!-- Hero Section -->
<section class="relative pt-24 pb-32 overflow-hidden">
    <!-- Abstract ambient glow - strictly no purple/blue, just a subtle zinc/amber presence -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-amber-500/5 rounded-full blur-[120px] pointer-events-none"></div>
    
    <div class="max-w-[1400px] mx-auto px-6 relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-zinc-800 bg-zinc-900/50 text-xs font-medium text-amber-500 mb-8 tracking-wide uppercase">
                <i class="ph ph-shield-check text-base"></i>
                Industrial Grade Certified
            </div>
            
            <h1 class="text-5xl md:text-7xl font-semibold text-zinc-50 tracking-tighter leading-[1.1] mb-8">
                Engineered timber for <span class="text-amber-500">structural integrity.</span>
            </h1>
            
            <p class="text-lg text-zinc-400 max-w-[55ch] leading-relaxed mb-10">
                Supply chain reliability meets precision engineering. Discover our premium Laminated Veneer Lumber, Heavy Duty Pallets, and precision Finger Joints designed for global export.
            </p>
            
            <div class="flex items-center gap-4">
                <a href="#products" class="bg-amber-500 hover:bg-amber-400 text-zinc-950 px-8 py-4 font-semibold rounded-sm transition-colors flex items-center gap-2">
                    View Catalog
                    <i class="ph ph-arrow-right font-bold text-lg"></i>
                </a>
                <a href="#" class="px-8 py-4 font-semibold text-zinc-300 hover:text-white hover:bg-zinc-800/50 border border-zinc-800 rounded-sm transition-colors">
                    Download Specs
                </a>
            </div>
        </div>
        
        <div class="relative hidden lg:block h-[600px]">
            <!-- Hero Image / Composition -->
            <div class="absolute inset-0 bg-zinc-900 border border-zinc-800 rounded-lg overflow-hidden">
                <img src="https://images.unsplash.com/photo-1610471242301-49931751d38f?q=80&w=2070&auto=format&fit=crop" alt="Industrial Timber" class="w-full h-full object-cover opacity-80 mix-blend-luminosity">
                <!-- Overlay gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/20 to-transparent"></div>
            </div>
            
            <!-- Floating Spec Card -->
            <div class="absolute bottom-8 left-8 p-6 bg-zinc-950/80 backdrop-blur-md border border-zinc-800 rounded-sm w-72">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Load Capacity</span>
                    <i class="ph ph-trend-up text-amber-500 text-lg"></i>
                </div>
                <div class="text-3xl font-semibold text-zinc-100 mb-1">4.5 Tons</div>
                <div class="text-sm text-zinc-400">Tested under ISPM-15 standards</div>
            </div>
        </div>
    </div>
</section>

<!-- Products Grid Section -->
<section id="products" class="py-24 bg-zinc-900/30 border-t border-zinc-800/50">
    <div class="max-w-[1400px] mx-auto px-6">
        <div class="flex items-end justify-between mb-16">
            <div>
                <h2 class="text-3xl md:text-4xl font-semibold text-zinc-50 tracking-tight mb-4">Industrial Catalog</h2>
                <p class="text-zinc-400 max-w-[60ch]">High-performance wood products manufactured to exact specifications.</p>
            </div>
            <a href="#" class="hidden md:flex items-center gap-2 text-amber-500 font-medium hover:text-amber-400 transition-colors">
                View all products <i class="ph ph-arrow-right"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                // Fetch products from Lunar (ignoring variants for simplicity on homepage)
                $products = \Lunar\Models\Product::with(['media'])->get();
            @endphp
            
            @foreach($products as $product)
            <div class="group bg-zinc-950 border border-zinc-800 hover:border-amber-500/50 transition-colors rounded-sm overflow-hidden flex flex-col">
                <div class="aspect-[4/3] bg-zinc-900 relative overflow-hidden">
                    @if($media = $product->media->first())
                        <img src="{{ $media->getUrl() }}" alt="{{ $product->translateAttribute('name') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-700">
                            <i class="ph ph-image text-4xl"></i>
                        </div>
                    @endif
                </div>
                
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-semibold text-zinc-100">{{ $product->translateAttribute('name') }}</h3>
                        @php
                            $variant = $product->variants->first();
                            $price = $variant ? $variant->prices->first() : null;
                        @endphp
                        @if($price)
                        <span class="text-lg font-medium text-amber-500">${{ number_format($price->price->value / 100, 2) }}</span>
                        @endif
                    </div>
                    
                    <p class="text-sm text-zinc-400 mb-8 line-clamp-2">
                        {{ $product->translateAttribute('description') }}
                    </p>
                    
                    <div class="mt-auto pt-6 border-t border-zinc-800/50 flex items-center justify-between">
                        <span class="text-xs font-mono text-zinc-500">SKU: {{ $variant ? $variant->sku : 'N/A' }}</span>
                        <a href="#" class="text-sm font-medium text-zinc-300 hover:text-amber-500 transition-colors flex items-center gap-1">
                            Specs <i class="ph ph-caret-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Stats/Trust Section -->
<section class="py-24 border-t border-zinc-800/50">
    <div class="max-w-[1400px] mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 border border-zinc-800/50 bg-zinc-900/20 rounded-sm p-8 md:p-12">
            <div>
                <div class="text-4xl font-semibold text-zinc-50 mb-2">25+</div>
                <div class="text-sm text-zinc-400 uppercase tracking-wide">Years Experience</div>
            </div>
            <div>
                <div class="text-4xl font-semibold text-zinc-50 mb-2">ISO</div>
                <div class="text-sm text-zinc-400 uppercase tracking-wide">9001:2015 Certified</div>
            </div>
            <div>
                <div class="text-4xl font-semibold text-zinc-50 mb-2">50k</div>
                <div class="text-sm text-zinc-400 uppercase tracking-wide">Tons Exported/Yr</div>
            </div>
            <div>
                <div class="text-4xl font-semibold text-zinc-50 mb-2">ISPM-15</div>
                <div class="text-sm text-zinc-400 uppercase tracking-wide">Compliant Wood</div>
            </div>
        </div>
    </div>
</section>
@endsection
