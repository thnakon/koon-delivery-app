import { create } from 'zustand';
import { MenuItem } from '@/services/menu';

export type CartItem = MenuItem & {
  cartQuantity: number;
  special_instructions?: string;
};

type CartState = {
  items: CartItem[];
  addItem: (item: MenuItem, quantity: number, instructions?: string) => void;
  removeItem: (itemId: number) => void;
  updateQuantity: (itemId: number, quantity: number) => void;
  clearCart: () => void;
  getCartTotal: () => number;
};

export const useCartStore = create<CartState>((set, get) => ({
  items: [],
  addItem: (item, quantity, instructions) => {
    set((state) => {
      const existingItem = state.items.find((i) => i.id === item.id);
      if (existingItem) {
        return {
          items: state.items.map((i) =>
            i.id === item.id ? { ...i, cartQuantity: i.cartQuantity + quantity, special_instructions: instructions || i.special_instructions } : i
          ),
        };
      }
      return { items: [...state.items, { ...item, cartQuantity: quantity, special_instructions: instructions }] };
    });
  },
  removeItem: (itemId) => {
    set((state) => ({
      items: state.items.filter((i) => i.id !== itemId),
    }));
  },
  updateQuantity: (itemId, quantity) => {
    if (quantity <= 0) {
      get().removeItem(itemId);
      return;
    }
    set((state) => ({
      items: state.items.map((i) => (i.id === itemId ? { ...i, cartQuantity: quantity } : i)),
    }));
  },
  clearCart: () => set({ items: [] }),
  getCartTotal: () => {
    return get().items.reduce((total, item) => total + parseFloat(item.price) * item.cartQuantity, 0);
  },
}));
