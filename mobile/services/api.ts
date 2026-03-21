import axios from 'axios';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

const API_DEV_URL = Platform.OS === 'android' ? 'http://10.0.2.2:8000/api' : 'http://localhost:8000/api';

export const api = axios.create({
  baseURL: process.env.EXPO_PUBLIC_API_URL || API_DEV_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Interceptor to attach token automatically
api.interceptors.request.use(async (config) => {
  try {
    let token;
    if (Platform.OS === 'web') {
      token = localStorage.getItem('auth_token');
    } else {
      token = await SecureStore.getItemAsync('auth_token');
    }
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
  } catch (error) {
    //
  }
  return config;
});
