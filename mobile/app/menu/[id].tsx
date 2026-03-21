import React, { useState } from 'react';
import { View, Text, StyleSheet, Image, TouchableOpacity, ActivityIndicator, TextInput } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useQuery } from '@tanstack/react-query';
import { api } from '@/services/api';
import { MenuItem } from '@/services/menu';
import { useCartStore } from '@/stores/useCartStore';

const fetchMenuItem = async (id: string): Promise<MenuItem> => {
  const { data } = await api.get(`/menu/${id}`);
  return data;
};

export default function MenuDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const addItem = useCartStore((state) => state.addItem);
  
  const [quantity, setQuantity] = useState(1);
  const [instructions, setInstructions] = useState('');

  const { data: item, isLoading } = useQuery({
    queryKey: ['menuItem', id],
    queryFn: () => fetchMenuItem(id),
  });

  if (isLoading || !item) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color="#000" />
      </View>
    );
  }

  const handleAddToCart = () => {
    addItem(item, quantity, instructions);
    router.back();
  };

  return (
    <View style={styles.container}>
      {item.image_url ? (
        <Image source={{ uri: item.image_url }} style={styles.image} />
      ) : (
        <View style={styles.placeholderImage} />
      )}
      
      <View style={styles.detailsContainer}>
        <Text style={styles.name}>{item.name}</Text>
        <Text style={styles.price}>฿{Number(item.price).toFixed(2)}</Text>
        {item.description ? <Text style={styles.description}>{item.description}</Text> : null}
        
        <View style={styles.quantityContainer}>
          <TouchableOpacity onPress={() => setQuantity(Math.max(1, quantity - 1))} style={styles.qtyBtn}>
            <Text style={styles.qtyBtnText}>-</Text>
          </TouchableOpacity>
          <Text style={styles.qtyText}>{quantity}</Text>
          <TouchableOpacity onPress={() => setQuantity(quantity + 1)} style={styles.qtyBtn}>
            <Text style={styles.qtyBtnText}>+</Text>
          </TouchableOpacity>
        </View>

        <TextInput
          style={styles.instructionInput}
          placeholder="Special instructions (e.g. no onions)"
          value={instructions}
          onChangeText={setInstructions}
          multiline
        />
      </View>

      <View style={styles.footer}>
        <TouchableOpacity style={styles.addToCartBtn} onPress={handleAddToCart}>
          <Text style={styles.addToCartText}>Add to Cart - ฿{(Number(item.price) * quantity).toFixed(2)}</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  image: { width: '100%', height: 250, resizeMode: 'cover' },
  placeholderImage: { width: '100%', height: 250, backgroundColor: '#eee' },
  detailsContainer: { padding: 20 },
  name: { fontSize: 24, fontWeight: 'bold', marginBottom: 5 },
  price: { fontSize: 20, color: '#000', fontWeight: '600', marginBottom: 15 },
  description: { fontSize: 16, color: '#666', marginBottom: 20, lineHeight: 22 },
  quantityContainer: { flexDirection: 'row', alignItems: 'center', marginBottom: 20 },
  qtyBtn: { width: 40, height: 40, backgroundColor: '#f0f0f0', borderRadius: 20, justifyContent: 'center', alignItems: 'center' },
  qtyBtnText: { fontSize: 20, fontWeight: 'bold' },
  qtyText: { fontSize: 18, fontWeight: 'bold', marginHorizontal: 20 },
  instructionInput: { borderWidth: 1, borderColor: '#ddd', borderRadius: 8, padding: 12, height: 80, textAlignVertical: 'top' },
  footer: { position: 'absolute', bottom: 0, left: 0, right: 0, padding: 20, backgroundColor: '#fff', borderTopWidth: 1, borderTopColor: '#eee' },
  addToCartBtn: { backgroundColor: '#000', padding: 15, borderRadius: 8, alignItems: 'center' },
  addToCartText: { color: '#fff', fontSize: 16, fontWeight: 'bold' },
});
