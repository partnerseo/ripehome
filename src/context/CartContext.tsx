import { createContext, useContext, useState, useEffect, type ReactNode } from 'react';
import type { WholesaleOrderItem } from '../types/api';

const STORAGE_KEY = 'ripehome_cart';

interface CartContextType {
  cart: WholesaleOrderItem[];
  addToCart: (item: WholesaleOrderItem) => void;
  updateQuantity: (productId: number, quantity: number, notes?: string) => void;
  removeFromCart: (productId: number) => void;
  clearCart: () => void;
  cartCount: number;
  cartTotal: number;
}

const CartContext = createContext<CartContextType | null>(null);

function loadCart(): WholesaleOrderItem[] {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    return saved ? JSON.parse(saved) : [];
  } catch {
    return [];
  }
}

export function CartProvider({ children }: { children: ReactNode }) {
  const [cart, setCart] = useState<WholesaleOrderItem[]>(loadCart);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
  }, [cart]);

  const addToCart = (item: WholesaleOrderItem) => {
    setCart(prev => {
      const existing = prev.findIndex(i => i.product_id === item.product_id);
      if (existing >= 0) {
        const updated = [...prev];
        updated[existing] = { ...updated[existing], quantity: item.quantity, notes: item.notes };
        return updated;
      }
      return [...prev, item];
    });
  };

  const updateQuantity = (productId: number, quantity: number, notes?: string) => {
    if (quantity <= 0) {
      removeFromCart(productId);
      return;
    }
    setCart(prev =>
      prev.map(item =>
        item.product_id === productId
          ? { ...item, quantity, ...(notes !== undefined ? { notes } : {}) }
          : item
      )
    );
  };

  const removeFromCart = (productId: number) => {
    setCart(prev => prev.filter(item => item.product_id !== productId));
  };

  const clearCart = () => {
    setCart([]);
  };

  const cartCount = cart.length;
  const cartTotal = cart.reduce((sum, item) => sum + item.quantity, 0);

  return (
    <CartContext.Provider value={{ cart, addToCart, updateQuantity, removeFromCart, clearCart, cartCount, cartTotal }}>
      {children}
    </CartContext.Provider>
  );
}

export function useCart() {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within CartProvider');
  }
  return context;
}
