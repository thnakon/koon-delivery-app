import { api } from './api';

export type Category = {
  id: number;
  name: string;
  slug: string;
  icon?: string;
};

export type MenuItem = {
  id: number;
  category_id: number;
  name: string;
  description?: string;
  price: string;
  image_url?: string;
  is_available: boolean;
  is_popular: boolean;
  prep_time_minutes: number;
};

export const fetchCategories = async (): Promise<Category[]> => {
  const { data } = await api.get('/categories');
  return data;
};

export const fetchMenuItems = async (): Promise<Record<string, MenuItem[]>> => {
  const { data } = await api.get('/menu');
  return data;
};
