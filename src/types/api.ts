export interface Category {
  id: number;
  name: string;
  slug: string;
  description?: string;
  image?: string;
  order: number;
  products_count?: number;
  created_at?: string;
}

export interface Tag {
  id: number;
  name: string;
  color: string;
}

export interface ProductFeature {
  icon?: string;
  title: string;
  description?: string;
}

export interface Product {
  id: number;
  name: string;
  slug: string;
  description: string;
  images: string[];
  category?: Category;
  tags?: Tag[];
  features?: ProductFeature[];
  is_featured: boolean;
  order: number;
  meta_title?: string;
  meta_description?: string;
  created_at?: string;
  updated_at?: string;
}

export interface HomeSlider {
  id: number;
  title: string;
  subtitle?: string;
  button_text?: string;
  button_link?: string;
  image: string;
  order: number;
}

export interface FeaturedSection {
  id: number;
  title: string;
  description?: string;
  image?: string;
  icon?: string;
  link?: string;
  order: number;
}

export interface FeaturedProduct {
  id: number;
  category_label?: string;
  title: string;
  description: string;
  image?: string;
  tags?: string[];
  button_text: string;
  button_link: string;
}

export interface Page {
  id: number;
  title: string;
  slug: string;
  content: string;
  meta_title?: string;
  meta_description?: string;
  created_at?: string;
  updated_at?: string;
}

export interface Settings {
  logo?: string;
  favicon?: string;
  phone?: string;
  email?: string;
  address?: string;
  social_media?: {
    facebook?: string;
    instagram?: string;
    twitter?: string;
    linkedin?: string;
  };
  footer_text?: string;
}

export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
  meta?: PaginationMeta;
}

export interface WholesaleOrderItem {
  product_id: number;
  product_name?: string;
  product_slug?: string;
  product_image?: string;
  quantity: number;
  notes?: string;
}

export interface WholesaleOrderForm {
  company_name: string;
  contact_person: string;
  email: string;
  phone: string;
  address?: string;
  city?: string;
  tax_office?: string;
  tax_number?: string;
  items: WholesaleOrderItem[];
  additional_notes?: string;
}


