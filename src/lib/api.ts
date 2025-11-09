import type { Category, Product, HomeSlider, FeaturedSection, FeaturedProduct, Page, Settings, ApiResponse, WholesaleOrderForm } from '../types/api';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080/api';

// Kategorileri çek
export async function getCategories(): Promise<Category[]> {
  try {
    const response = await fetch(`${API_URL}/categories`);
    const data: ApiResponse<Category[]> = await response.json();
    
    console.log('✅ Categories API response:', data);
    console.log('📦 Total categories:', data.data?.length);
    
    if (data.success && data.data) {
      data.data.forEach(cat => {
        console.log(`  - ${cat.name}: ${cat.products_count || 0} products`);
      });
      return data.data;
    }
    
    return [];
  } catch (error) {
    console.error('❌ Categories API error:', error);
    return [];
  }
}

// Tekil kategori
export async function getCategory(slug: string): Promise<Category | null> {
  try {
    const response = await fetch(`${API_URL}/categories/${slug}`);
    const data: ApiResponse<Category> = await response.json();
    return data.success ? data.data : null;
  } catch (error) {
    console.error('Kategori yüklenemedi:', error);
    return null;
  }
}

// Ürünleri çek
export async function getProducts(page = 1) {
  try {
    const response = await fetch(`${API_URL}/products?page=${page}`);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Ürünler yüklenemedi:', error);
    return { success: false, data: [], meta: null };
  }
}

// Kategoriye göre ürünler
export async function getProductsByCategory(categorySlug: string, page = 1, perPage = 100) {
  try {
    const response = await fetch(
      `${API_URL}/products/category/${categorySlug}?page=${page}&per_page=${perPage}`
    );
    const data = await response.json();
    
    console.log(`✅ Category products API response:`, {
      category: data.category?.name,
      products: data.data?.length,
      total: data.meta?.total,
      currentPage: data.meta?.current_page,
      lastPage: data.meta?.last_page,
    });
    
    if (data.success) {
      return {
        products: data.data || [],
        category: data.category,
        meta: data.meta
      };
    }
    
    return { products: [], category: null, meta: null };
  } catch (error) {
    console.error('❌ Category products error:', error);
    return { products: [], category: null, meta: null };
  }
}

// Tekil ürün
export async function getProduct(slug: string): Promise<Product | null> {
  try {
    const response = await fetch(`${API_URL}/products/${slug}`);
    const data: ApiResponse<Product> = await response.json();
    return data.success ? data.data : null;
  } catch (error) {
    console.error('Ürün yüklenemedi:', error);
    return null;
  }
}

// Anasayfa slider
export async function getHomeSliders(): Promise<HomeSlider[]> {
  try {
    const response = await fetch(`${API_URL}/home-sliders`);
    const data: ApiResponse<HomeSlider[]> = await response.json();
    return data.success ? data.data : [];
  } catch (error) {
    console.error('Slider yüklenemedi:', error);
    return [];
  }
}

// Öne çıkan bölümler
export async function getFeaturedSections(): Promise<FeaturedSection[]> {
  try {
    const response = await fetch(`${API_URL}/featured-sections`);
    const data: ApiResponse<FeaturedSection[]> = await response.json();
    return data.success ? data.data : [];
  } catch (error) {
    console.error('Öne çıkan bölümler yüklenemedi:', error);
    return [];
  }
}

// Öne çıkan ürünler (Büyük kartlar)
export async function getFeaturedProducts(): Promise<FeaturedProduct[]> {
  console.log('📞 getFeaturedProducts() called');
  try {
    const url = `${API_URL}/featured-products`;
    console.log('🌐 Fetching:', url);
    
    const response = await fetch(url);
    console.log('📡 Response status:', response.status);
    
    const data: ApiResponse<FeaturedProduct[]> = await response.json();
    console.log('📦 Featured products data:', data);
    console.log('✅ Featured products count:', data.data?.length || 0);
    
    return data.success ? data.data : [];
  } catch (error) {
    console.error('❌ Öne çıkan ürünler yüklenemedi:', error);
    return [];
  }
}

// Sayfa içeriği
export async function getPage(slug: string): Promise<Page | null> {
  try {
    const response = await fetch(`${API_URL}/pages/${slug}`);
    const data: ApiResponse<Page> = await response.json();
    return data.success ? data.data : null;
  } catch (error) {
    console.error('Sayfa yüklenemedi:', error);
    return null;
  }
}

// Site ayarları
export async function getSettings(): Promise<Settings | null> {
  try {
    const response = await fetch(`${API_URL}/settings`);
    const data: ApiResponse<Settings> = await response.json();
    return data.success ? data.data : null;
  } catch (error) {
    console.error('Ayarlar yüklenemedi:', error);
    return null;
  }
}

// İletişim formu gönder
export async function submitContact(formData: {
  name: string;
  email: string;
  subject?: string;
  message: string;
}) {
  try {
    const response = await fetch(`${API_URL}/contact`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(formData),
    });
    return response.json();
  } catch (error) {
    console.error('İletişim formu gönderilemedi:', error);
    return { success: false, message: 'Bir hata oluştu' };
  }
}

// Toptan sipariş oluştur
export async function createWholesaleOrder(data: WholesaleOrderForm) {
  try {
    const response = await fetch(`${API_URL}/wholesale-orders`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
    return response.json();
  } catch (error) {
    console.error('Toptan sipariş gönderilemedi:', error);
    return { success: false, message: 'Bir hata oluştu' };
  }
}


