export interface LunarProduct {
  id: string;
  name: string;
  sku: string;
  price: number; // in cents
  formattedPrice: string;
  description: string;
  imageUrl: string;
}

export const MOCK_PRODUCTS: LunarProduct[] = [
  {
    id: '1',
    sku: 'LVL-BEAM-001',
    name: 'Premium LVL Timber Beam',
    price: 15000,
    formattedPrice: '$150.00',
    description: 'High strength Laminated Veneer Lumber (LVL) ideal for structural applications.',
    imageUrl: '/laminated_wood.png'
  },
  {
    id: '2',
    sku: 'PALLET-001',
    name: 'Heavy Duty Solid Wood Pallet',
    price: 4500,
    formattedPrice: '$45.00',
    description: 'Industrial grade solid wood pallet designed for heavy loads and export.',
    imageUrl: '/solid_pallet.png'
  },
  {
    id: '3',
    sku: 'FINGER-JOINT-001',
    name: 'Precision Finger Joint',
    price: 1250,
    formattedPrice: '$12.50',
    description: 'Flawlessly engineered finger joint timber for seamless construction.',
    imageUrl: '/finger_joint.png'
  }
];

export async function fetchLiveProducts(): Promise<LunarProduct[]> {
  // During production builds (for static export), do not call the local backend.
  if (process.env.NODE_ENV === 'production') {
    return MOCK_PRODUCTS;
  }

  try {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 3000); // 3 second timeout
    const res = await fetch('http://localhost:8000/api/products', {
      cache: 'no-store',
      signal: controller.signal,
    });
    clearTimeout(timeout);
    if (res.ok) {
      const json = await res.json();
      if (json.data && json.data.length > 0) {
        return json.data.map((p: any) => {
          // Detect appropriate local image based on title/slug
          let localImg = '/hero_timber.png';
          const titleLower = p.title.toLowerCase();
          if (titleLower.includes('beam') || titleLower.includes('laminated') || titleLower.includes('lvl')) {
            localImg = '/laminated_wood.png';
          } else if (titleLower.includes('pallet')) {
            localImg = '/solid_pallet.png';
          } else if (titleLower.includes('joint') || titleLower.includes('finger')) {
            localImg = '/finger_joint.png';
          }

          return {
            id: p.id,
            name: p.title,
            sku: p.slug,
            price: p.price * 100,
            formattedPrice: `${p.currency === 'USD' ? '$' : p.currency} ${p.price}`,
            description: p.desc,
            imageUrl: p.img || localImg
          };
        });
      }
    }
  } catch (error) {
    console.error("Backend unavailable, using mock data.", error);
  }
  
  return MOCK_PRODUCTS;
}

export async function fetchProductBySku(sku: string): Promise<LunarProduct | null> {
  const products = await fetchLiveProducts();
  let product = products.find((p) => p.sku.toLowerCase() === sku.toLowerCase());
  
  // If not found in live products (e.g. because backend is running and only returned backend products),
  // fallback to searching the mock products.
  if (!product) {
    product = MOCK_PRODUCTS.find((p) => p.sku.toLowerCase() === sku.toLowerCase());
  }
  
  return product || null;
}
