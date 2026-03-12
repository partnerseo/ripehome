import type { Category, Product, HomeSlider, FeaturedSection, FeaturedProduct, Page, Settings, ApiResponse, WholesaleOrderForm } from '../types/api';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

// Kategorileri çek
export async function getCategories(): Promise<Category[]> {
  try {
    const response = await fetch(`${API_URL}/kategoriler`);
    const data: ApiResponse<Category[]> = await response.json();
    if (data.success && data.data) {
      return data.data;
    }
    return [];
  } catch (error) {
    console.error('Kategoriler yüklenemedi:', error);
    return [];
  }
}

// Tekil kategori
export async function getCategory(slug: string): Promise<Category | null> {
  try {
    const response = await fetch(`${API_URL}/kategoriler/${slug}`);
    const data: ApiResponse<Category> = await response.json();
    return data.success ? data.data : null;
  } catch (error) {
    console.error('Kategori yüklenemedi:', error);
    return null;
  }
}

// Ürünleri çek
export async function getProducts(page = 1, perPage = 12) {
  try {
    const response = await fetch(`${API_URL}/urunler?page=${page}&per_page=${perPage}`);
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
      `${API_URL}/urunler/kategori/${categorySlug}?page=${page}&per_page=${perPage}`
    );
    const data = await response.json();
    if (data.success) {
      return {
        products: data.data || [],
        category: data.category,
        meta: data.meta
      };
    }
    return { products: [], category: null, meta: null };
  } catch (error) {
    console.error('Kategori ürünleri yüklenemedi:', error);
    return { products: [], category: null, meta: null };
  }
}

// Tekil ürün
export async function getProduct(slug: string): Promise<Product | null> {
  try {
    const response = await fetch(`${API_URL}/urunler/${slug}`);
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
    const response = await fetch(`${API_URL}/anasayfa-slider`);
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
    const response = await fetch(`${API_URL}/one-cikan-bolumler`);
    const data: ApiResponse<FeaturedSection[]> = await response.json();
    return data.success ? data.data : [];
  } catch (error) {
    console.error('Öne çıkan bölümler yüklenemedi:', error);
    return [];
  }
}

// Öne çıkan ürünler
export async function getFeaturedProducts(): Promise<FeaturedProduct[]> {
  try {
    const response = await fetch(`${API_URL}/one-cikan-urunler`);
    const data: ApiResponse<FeaturedProduct[]> = await response.json();
    return data.success ? data.data : [];
  } catch (error) {
    console.error('Öne çıkan ürünler yüklenemedi:', error);
    return [];
  }
}

// Sayfa içeriği
export async function getPage(slug: string): Promise<Page | null> {
  try {
    const response = await fetch(`${API_URL}/sayfalar/${slug}`);
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
    const response = await fetch(`${API_URL}/ayarlar`);
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
    const response = await fetch(`${API_URL}/iletisim`, {
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
    const response = await fetch(`${API_URL}/toptan-siparisler`, {
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
