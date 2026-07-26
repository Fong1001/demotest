export interface AimeosProduct {
  id: string;
  type: string;
  attributes: {
    'product.code': string;
    'product.label': string;
    'product.status': number;
  };
  relationships?: {
    media?: { data: { id: string; type: string }[] };
    price?: { data: { id: string; type: string }[] };
  };
}

export interface AimeosMedia {
  id: string;
  type: string;
  attributes: {
    'media.url': string;
    'media.preview': string;
  };
}

export interface AimeosPrice {
  id: string;
  type: string;
  attributes: {
    'price.value': string;
    'price.currencyid': string;
  };
}

export interface AimeosResponse {
  data: AimeosProduct[];
  included?: any[];
}

export async function fetchLiveProducts() {
  const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/jsonapi';
  
  try {
    // Fetch products and include their related media and prices
    const response = await fetch(`${apiUrl}/product?include=media,price`, {
      // Use next.js caching for 60 seconds
      next: { revalidate: 60 }
    });

    if (!response.ok) {
      throw new Error(`Failed to fetch from Aimeos: ${response.statusText}`);
    }

    const json: AimeosResponse = await response.json();
    const products = json.data || [];
    
    // Parse included data (Media and Price) to map them to the products
    const mediaMap = new Map<string, string>();
    const priceMap = new Map<string, string>();

    if (json.included) {
      json.included.forEach((item) => {
        if (item.type === 'media') {
          // Aimeos media URL might be absolute or relative
          let url = item.attributes['media.url'];
          if (url.startsWith('/')) {
             url = `http://localhost:8000${url}`;
          }
          mediaMap.set(item.id, url);
        } else if (item.type === 'price') {
          const val = parseFloat(item.attributes['price.value']).toFixed(2);
          const cur = item.attributes['price.currencyid'] || 'USD';
          priceMap.set(item.id, `${cur} ${val}`);
        }
      });
    }

    // Format the products for our UI
    return products.map((product) => {
      let imageUrl = '/placeholder.png'; // Fallback
      let priceStr = 'Price not set';

      // Find first image
      const mediaRefs = product.relationships?.media?.data || [];
      if (mediaRefs.length > 0) {
        imageUrl = mediaMap.get(mediaRefs[0].id) || imageUrl;
      }

      // Find first price
      const priceRefs = product.relationships?.price?.data || [];
      if (priceRefs.length > 0) {
        priceStr = priceMap.get(priceRefs[0].id) || priceStr;
      }

      return {
        id: product.id,
        name: product.attributes['product.label'],
        code: product.attributes['product.code'],
        imageUrl,
        price: priceStr,
      };
    });

  } catch (error) {
    console.error('Error fetching Aimeos products:', error);
    return []; // Return empty array on failure so frontend doesn't crash
  }
}
