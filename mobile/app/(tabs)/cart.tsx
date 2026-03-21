import React from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, Image } from 'react-native';
import { useCartStore, CartItem } from '@/stores/useCartStore';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';

export default function CartScreen() {
  const { items, updateQuantity, getCartTotal } = useCartStore();
  const router = useRouter();

  const renderItem = ({ item }: { item: CartItem }) => (
    <View style={styles.cartItem}>
      <View style={styles.itemInfo}>
        <Text style={styles.itemName}>{item.name}</Text>
        <Text style={styles.itemPrice}>฿{Number(item.price).toFixed(2)}</Text>
        {item.special_instructions ? <Text style={styles.itemInstruct}>Note: {item.special_instructions}</Text> : null}
      </View>
      <View style={styles.qtyControls}>
        <TouchableOpacity onPress={() => updateQuantity(item.id, item.cartQuantity - 1)} style={styles.qtyBtn}>
          <Text style={styles.qtyBtnText}>-</Text>
        </TouchableOpacity>
        <Text style={styles.qtyText}>{item.cartQuantity}</Text>
        <TouchableOpacity onPress={() => updateQuantity(item.id, item.cartQuantity + 1)} style={styles.qtyBtn}>
          <Text style={styles.qtyBtnText}>+</Text>
        </TouchableOpacity>
      </View>
    </View>
  );

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      <Text style={styles.headerTitle}>Your Cart</Text>
      
      <FlatList
        data={items}
        renderItem={renderItem}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContainer}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyText}>Your cart is empty.</Text>
          </View>
        }
      />

      {items.length > 0 && (
        <View style={styles.footer}>
          <View style={styles.totalRow}>
            <Text style={styles.totalLabel}>Total</Text>
            <Text style={styles.totalValue}>฿{getCartTotal().toFixed(2)}</Text>
          </View>
          <TouchableOpacity 
            style={styles.checkoutBtn} 
            onPress={() => router.push('/checkout')}
          >
            <Text style={styles.checkoutBtnText}>Proceed to Checkout</Text>
          </TouchableOpacity>
        </View>
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f9f9f9' },
  headerTitle: { fontSize: 24, fontWeight: 'bold', padding: 20, paddingBottom: 10 },
  listContainer: { padding: 15 },
  emptyContainer: { alignItems: 'center', marginTop: 50 },
  emptyText: { fontSize: 16, color: '#999' },
  cartItem: { flexDirection: 'row', backgroundColor: '#fff', padding: 15, borderRadius: 12, marginBottom: 10, alignItems: 'center' },
  itemInfo: { flex: 1 },
  itemName: { fontSize: 16, fontWeight: '600', marginBottom: 4 },
  itemPrice: { fontSize: 15, color: '#666' },
  itemInstruct: { fontSize: 12, color: '#888', marginTop: 4, fontStyle: 'italic' },
  qtyControls: { flexDirection: 'row', alignItems: 'center' },
  qtyBtn: { width: 30, height: 30, backgroundColor: '#f0f0f0', borderRadius: 15, justifyContent: 'center', alignItems: 'center' },
  qtyBtnText: { fontSize: 18, fontWeight: 'bold' },
  qtyText: { fontSize: 16, fontWeight: '600', marginHorizontal: 15 },
  footer: { padding: 20, backgroundColor: '#fff', borderTopWidth: 1, borderTopColor: '#eee' },
  totalRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15 },
  totalLabel: { fontSize: 18, fontWeight: 'bold', color: '#666' },
  totalValue: { fontSize: 22, fontWeight: 'bold', color: '#000' },
  checkoutBtn: { backgroundColor: '#000', padding: 15, borderRadius: 8, alignItems: 'center' },
  checkoutBtnText: { color: '#fff', fontSize: 16, fontWeight: 'bold' },
});
