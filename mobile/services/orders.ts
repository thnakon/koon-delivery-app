import { api } from './api';

export type OrderPayload = {
  type: 'pickup' | 'delivery';
  items: Array<{
    menu_item_id: number;
    quantity: number;
    special_instructions?: string;
  }>;
  coupon_code?: string;
  note?: string;
  delivery_address?: string;
  delivery_lat?: number;
  delivery_lng?: number;
  idempotency_key: string;
};

export const createOrder = async (payload: OrderPayload) => {
  const { data } = await api.post('/orders', payload);
  return data;
};

export const getOrders = async () => {
  const { data } = await api.get('/orders');
  return data;
};

export const getOrderDetails = async (id: string | number) => {
  const { data } = await api.get(`/orders/${id}`);
  return data;
};
