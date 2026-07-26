export interface LunarProduct {
  id: string;
  name: string;
  sku: string;
  price: number; // in cents
  formattedPrice: string;
  description: string;
  imageUrl: string;
}

export async function fetchLiveProducts(): Promise<LunarProduct[]> {
  try {
    const res = await fetch('http://localhost:8000/api/products', { cache: 'no-store' });
    if (res.ok) {
      const json = await res.json();
      if (json.data && json.data.length > 0) {
        return json.data.map((p: any) => ({
          id: p.id,
          name: p.title,
          sku: p.slug,
          price: p.price * 100, // assuming backend returns decimal
          formattedPrice: `${p.currency === 'USD' ? '$' : p.currency} ${p.price}`,
          description: p.desc,
          imageUrl: p.img || 'https://placehold.co/600x400/e9c176/412d00?text=' + encodeURIComponent(p.title)
        }));
      }
    }
  } catch (error) {
    console.error("Failed to fetch from Lunar API, falling back to mock data.", error);
  }
  
  // Fallback data
  return [
    {
      id: '1',
      sku: 'LVL-BEAM-001',
      name: 'Premium LVL Timber Beam',
      price: 15000,
      formattedPrice: '$150.00',
      description: 'High strength Laminated Veneer Lumber (LVL) ideal for structural applications.',
      imageUrl: 'https://placehold.co/600x400/e9c176/412d00?text=LVL+Beam'
    },
    {
      id: '2',
      sku: 'PALLET-001',
      name: 'Heavy Duty Solid Wood Pallet',
      price: 4500,
      formattedPrice: '$45.00',
      description: 'Industrial grade solid wood pallet designed for heavy loads and export.',
      imageUrl: 'https://placehold.co/600x400/e9c176/412d00?text=Solid+Pallet'
    },
    {
      id: '3',
      sku: 'FINGER-JOINT-001',
      name: 'Precision Finger Joint',
      price: 1250,
      formattedPrice: '$12.50',
      description: 'Flawlessly engineered finger joint timber for seamless construction.',
      imageUrl: 'https://placehold.co/600x400/e9c176/412d00?text=Finger+Joint'
    }
  ];
}

export async function fetchProductBySku(sku: string): Promise<LunarProduct | null> {
  const products = await fetchLiveProducts();
  const product = products.find((p) => p.sku.toLowerCase() === sku.toLowerCase());
  return product || null;
}
