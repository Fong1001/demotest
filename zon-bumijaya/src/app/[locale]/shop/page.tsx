import { fetchLiveProducts } from '@/lib/api/lunar';
import Image from 'next/image';
import Link from 'next/link';

export default async function ShopDemoPage() {
  const products = await fetchLiveProducts();

  return (
    <div className="min-h-screen bg-background text-on-background pb-24">
      {/* Simple Header */}
      <header className="bg-surface-variant py-8 px-gutter border-b border-white/5">
        <div className="max-w-container-max mx-auto flex justify-between items-center">
          <Link href="/" className="font-display text-primary text-headline-md tracking-tighter hover:text-white transition-colors">
            Zon Bumijaya
          </Link>
          <span className="font-body text-xs uppercase tracking-widest text-primary border border-primary/30 px-3 py-1 rounded-full">
            Lunar PHP Storefront
          </span>
        </div>
      </header>

      <main className="max-w-container-max mx-auto px-gutter pt-16">
        <div className="mb-16 flex flex-col md:flex-row justify-between items-end">
          <div>
            <h1 className="font-display text-headline-xl text-white mb-4">Timber Catalog</h1>
            <p className="font-body text-on-surface-variant max-w-2xl text-lg">
              Explore our premium structural timber and pallets. 
            </p>
          </div>
          
          {/* Mock filters for B2B */}
          <div className="mt-8 md:mt-0 flex gap-4">
            <select className="bg-surface-variant border border-white/10 text-white font-body py-2 px-4 rounded hover:border-primary transition-colors outline-none focus:border-primary">
              <option>All Grades</option>
              <option>Structural (S1/S2)</option>
              <option>Appearance (A/B)</option>
            </select>
            <select className="bg-surface-variant border border-white/10 text-white font-body py-2 px-4 rounded hover:border-primary transition-colors outline-none focus:border-primary">
              <option>Sort by Default</option>
              <option>Price: Low to High</option>
              <option>Price: High to Low</option>
            </select>
          </div>
        </div>

        {products.length === 0 ? (
          <div className="bg-surface-variant p-12 text-center border border-white/5">
            <h3 className="font-display text-2xl text-white mb-2">No Products Found</h3>
            <p className="font-body text-on-surface-variant">Could not fetch products from Lunar.</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            {products.map((product) => (
              <Link href={`/shop/${product.sku}`} key={product.id} className="group flex flex-col bg-surface-variant border border-white/5 hover:border-primary/50 transition-colors h-full">
                <div className="aspect-[4/3] relative overflow-hidden bg-white/5">
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img 
                    src={product.imageUrl} 
                    alt={product.name} 
                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                  />
                  <div className="absolute top-4 right-4 bg-black/60 backdrop-blur-md border border-white/10 px-2 py-1 rounded text-[10px] font-body text-white tracking-widest uppercase">
                    In Stock
                  </div>
                </div>
                
                <div className="p-6 flex flex-col flex-grow">
                  <span className="font-body text-[10px] uppercase tracking-widest text-primary mb-2">
                    {product.sku}
                  </span>
                  <h3 className="font-display text-xl text-white mb-2 leading-tight">
                    {product.name}
                  </h3>
                  <p className="font-body text-sm text-on-surface-variant mb-6 line-clamp-2">
                    {product.description}
                  </p>
                  
                  <div className="mt-auto pt-4 border-t border-white/10 flex justify-between items-center">
                    <div className="flex flex-col">
                      <span className="text-[10px] text-on-surface-variant uppercase tracking-widest">Wholesale</span>
                      <span className="font-display font-bold text-lg text-white">
                        {product.formattedPrice}
                      </span>
                    </div>
                    <button className="bg-primary/10 text-primary border border-primary/20 hover:bg-primary hover:text-on-primary transition-colors text-xs font-body uppercase tracking-widest px-4 py-2 rounded">
                      Details
                    </button>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        )}
      </main>
    </div>
  );
}
