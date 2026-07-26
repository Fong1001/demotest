import { fetchProductBySku } from '@/lib/api/lunar';
import { notFound } from 'next/navigation';
import Link from 'next/link';

interface ProductPageProps {
  params: {
    locale: string;
    slug: string;
  };
}

export default async function ProductDetailPage({ params }: ProductPageProps) {
  const { slug } = await params;
  const product = await fetchProductBySku(slug);

  if (!product) {
    notFound();
  }

  return (
    <div className="min-h-screen bg-background text-on-background pb-24">
      {/* Simple Header */}
      <header className="bg-surface-dim py-8 px-gutter border-b border-white/5 mb-8">
        <div className="max-w-container-max mx-auto flex justify-between items-center">
          <Link href="/shop" className="font-body text-sm text-on-surface-variant hover:text-white transition-colors flex items-center gap-2">
            ← Back to Catalog
          </Link>
          <span className="font-body text-xs uppercase tracking-widest text-primary border border-primary/30 px-3 py-1 rounded-full">
            Lunar PHP
          </span>
        </div>
      </header>

      <main className="max-w-container-max mx-auto px-gutter grid grid-cols-1 lg:grid-cols-2 gap-16">
        {/* Product Images */}
        <div className="flex flex-col gap-4">
          <div className="aspect-[4/3] relative overflow-hidden bg-surface-variant border border-white/5">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img 
              src={product.imageUrl} 
              alt={product.name} 
              className="w-full h-full object-cover"
            />
          </div>
          {/* Thumbnails mockup */}
          <div className="grid grid-cols-4 gap-4">
             <div className="aspect-square bg-surface-variant border border-primary overflow-hidden opacity-100 cursor-pointer">
                 {/* eslint-disable-next-line @next/next/no-img-element */}
                 <img src={product.imageUrl} alt={product.name} className="w-full h-full object-cover" />
             </div>
             <div className="aspect-square bg-surface-variant border border-white/5 overflow-hidden opacity-50 cursor-pointer hover:opacity-100 transition-opacity">
                 {/* eslint-disable-next-line @next/next/no-img-element */}
                 <img src={product.imageUrl} alt={product.name} className="w-full h-full object-cover" />
             </div>
          </div>
        </div>

        {/* Product Info */}
        <div className="flex flex-col">
          <span className="font-body text-sm uppercase tracking-widest text-primary mb-4">
            SKU: {product.sku}
          </span>
          <h1 className="font-display text-4xl text-white mb-6 leading-tight">
            {product.name}
          </h1>
          
          <div className="flex items-end gap-4 mb-8 pb-8 border-b border-white/10">
            <span className="font-display font-bold text-3xl text-white">
              {product.formattedPrice}
            </span>
            <span className="font-body text-on-surface-variant mb-1">
              / Unit (Wholesale)
            </span>
          </div>

          <div className="mb-12">
            <h3 className="font-display text-xl text-white mb-4">Description</h3>
            <p className="font-body text-on-surface-variant text-lg leading-relaxed">
              {product.description}
            </p>
          </div>

          {/* B2B Technical Specs Mockup */}
          <div className="mb-12">
            <h3 className="font-display text-xl text-white mb-4">Technical Specifications</h3>
            <div className="grid grid-cols-2 gap-y-4 font-body border border-white/10 p-6 bg-surface-variant">
              <div className="text-on-surface-variant">Grade</div>
              <div className="text-white">Premium Structural</div>
              <div className="text-on-surface-variant">Moisture Content</div>
              <div className="text-white">&lt; 12% (Kiln Dried)</div>
              <div className="text-on-surface-variant">Treatment</div>
              <div className="text-white">Heat Treated (ISPM 15)</div>
            </div>
          </div>

          {/* Add to Cart (Mock) */}
          <div className="mt-auto flex gap-4">
            <div className="flex bg-surface-variant border border-white/20 rounded">
              <button className="px-4 py-3 text-white hover:text-primary transition-colors">-</button>
              <input type="number" defaultValue={10} className="w-16 bg-transparent text-center font-display text-xl text-white outline-none" />
              <button className="px-4 py-3 text-white hover:text-primary transition-colors">+</button>
            </div>
            <button className="flex-1 bg-primary text-on-primary font-body uppercase tracking-widest px-8 py-4 rounded hover:bg-primary/90 transition-colors">
              Add to Quote / Cart
            </button>
          </div>
        </div>
      </main>
    </div>
  );
}
