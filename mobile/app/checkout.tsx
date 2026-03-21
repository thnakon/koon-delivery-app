import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, TextInput, Alert, ScrollView } from 'react-native';
import { useRouter } from 'expo-router';
import { useCartStore } from '@/stores/useCartStore';
import { createOrder, OrderPayload } from '@/services/orders';
import { SafeAreaView } from 'react-native-safe-area-context';
import { api } from '@/services/api';

export default function CheckoutScreen() {
  const { items, getCartTotal, clearCart } = useCartStore();
  const router = useRouter();
  
  const [orderType, setOrderType] = useState<'pickup' | 'delivery'>('pickup');
  const [address, setAddress] = useState('');
  const [note, setNote] = useState('');
  const [couponCode, setCouponCode] = useState('');
  const [discount, setDiscount] = useState(0);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const subtotal = getCartTotal();
  const deliveryFee = orderType === 'delivery' ? 50 : 0;
  const total = Math.max(0, subtotal - discount) + deliveryFee;

  const handleApplyCoupon = async () => {
    if (!couponCode) return;
    try {
      const res = await api.post('/coupons/validate', { code: couponCode, subtotal });
      setDiscount(res.data.discount);
      Alert.alert('Success', 'Coupon applied successfully!');
    } catch (e: any) {
      Alert.alert('Error', e.response?.data?.message || 'Invalid coupon');
      setDiscount(0);
    }
  };

  const handlePlaceOrder = async () => {
    if (orderType === 'delivery' && !address.trim()) {
      Alert.alert('Error', 'Please enter a delivery address.');
      return;
    }

    try {
      setIsSubmitting(true);
      const idempotencyKey = new Date().getTime().toString() + Math.random().toString(36).substring(7);
      
      const payload: OrderPayload = {
        type: orderType,
        items: items.map(i => ({
          menu_item_id: i.id,
          quantity: i.cartQuantity,
          special_instructions: i.special_instructions
        })),
        coupon_code: couponCode || undefined,
        note,
        delivery_address: orderType === 'delivery' ? address : undefined,
        idempotency_key: idempotencyKey
      };

      const order = await createOrder(payload);
      clearCart();
      router.replace(`/order/${order.id}` as any);
    } catch (e: any) {
      Alert.alert('Error', e.response?.data?.message || 'Failed to place order');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <Text style={styles.headerTitle}>Checkout</Text>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Order Type</Text>
          <View style={styles.typeSelector}>
            <TouchableOpacity 
              style={[styles.typeBtn, orderType === 'pickup' && styles.typeBtnActive]}
              onPress={() => setOrderType('pickup')}
            >
              <Text style={[styles.typeBtnText, orderType === 'pickup' && styles.typeBtnTextActive]}>Pickup</Text>
            </TouchableOpacity>
            <TouchableOpacity 
              style={[styles.typeBtn, orderType === 'delivery' && styles.typeBtnActive]}
              onPress={() => setOrderType('delivery')}
            >
              <Text style={[styles.typeBtnText, orderType === 'delivery' && styles.typeBtnTextActive]}>Delivery</Text>
            </TouchableOpacity>
          </View>
        </View>

        {orderType === 'delivery' && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Delivery Address</Text>
            <TextInput
              style={styles.inputArea}
              placeholder="Enter your full delivery address"
              value={address}
              onChangeText={setAddress}
              multiline
            />
          </View>
        )}

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Additional Note (Optional)</Text>
          <TextInput
            style={styles.input}
            placeholder="E.g. Call upon arrival"
            value={note}
            onChangeText={setNote}
          />
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Coupon Code</Text>
          <View style={styles.couponRow}>
            <TextInput
              style={[styles.input, { flex: 1, marginBottom: 0 }]}
              placeholder="Enter code"
              value={couponCode}
              onChangeText={setCouponCode}
              autoCapitalize="characters"
            />
            <TouchableOpacity style={styles.applyBtn} onPress={handleApplyCoupon}>
              <Text style={styles.applyBtnText}>Apply</Text>
            </TouchableOpacity>
          </View>
        </View>

        <View style={styles.summaryBox}>
          <Text style={styles.summaryTitle}>Order Summary</Text>
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Subtotal</Text>
            <Text style={styles.summaryValue}>฿{subtotal.toFixed(2)}</Text>
          </View>
          {discount > 0 && (
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Discount</Text>
              <Text style={styles.summaryValue}>-฿{discount.toFixed(2)}</Text>
            </View>
          )}
          {deliveryFee > 0 && (
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Delivery Fee</Text>
              <Text style={styles.summaryValue}>฿{deliveryFee.toFixed(2)}</Text>
            </View>
          )}
          <View style={[styles.summaryRow, styles.totalRow]}>
            <Text style={styles.totalLabel}>Total</Text>
            <Text style={styles.totalValue}>฿{total.toFixed(2)}</Text>
          </View>
        </View>
      </ScrollView>

      <View style={styles.footer}>
        <TouchableOpacity 
          style={[styles.placeBtn, isSubmitting && styles.placeBtnDisabled]} 
          onPress={handlePlaceOrder}
          disabled={isSubmitting}
        >
          <Text style={styles.placeBtnText}>{isSubmitting ? 'Processing...' : 'Place Order'}</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f9f9f9' },
  scrollContent: { padding: 20, paddingBottom: 40 },
  headerTitle: { fontSize: 24, fontWeight: 'bold', marginBottom: 20 },
  section: { marginBottom: 25 },
  sectionTitle: { fontSize: 16, fontWeight: 'bold', marginBottom: 10, color: '#333' },
  typeSelector: { flexDirection: 'row', gap: 10 },
  typeBtn: { flex: 1, padding: 15, borderRadius: 8, borderWidth: 1, borderColor: '#ddd', alignItems: 'center', backgroundColor: '#fff' },
  typeBtnActive: { backgroundColor: '#000', borderColor: '#000' },
  typeBtnText: { fontSize: 16, fontWeight: '600', color: '#666' },
  typeBtnTextActive: { color: '#fff' },
  input: { borderWidth: 1, borderColor: '#ddd', borderRadius: 8, padding: 15, backgroundColor: '#fff', fontSize: 16 },
  inputArea: { borderWidth: 1, borderColor: '#ddd', borderRadius: 8, padding: 15, backgroundColor: '#fff', fontSize: 16, height: 100, textAlignVertical: 'top' },
  couponRow: { flexDirection: 'row', gap: 10 },
  applyBtn: { backgroundColor: '#333', paddingHorizontal: 20, justifyContent: 'center', borderRadius: 8 },
  applyBtnText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
  summaryBox: { backgroundColor: '#fff', padding: 20, borderRadius: 12, marginTop: 10 },
  summaryTitle: { fontSize: 18, fontWeight: 'bold', marginBottom: 15 },
  summaryRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 10 },
  summaryLabel: { fontSize: 16, color: '#666' },
  summaryValue: { fontSize: 16, fontWeight: '600' },
  totalRow: { borderTopWidth: 1, borderTopColor: '#eee', paddingTop: 15, marginTop: 5 },
  totalLabel: { fontSize: 18, fontWeight: 'bold' },
  totalValue: { fontSize: 22, fontWeight: 'bold' },
  footer: { padding: 20, backgroundColor: '#fff', borderTopWidth: 1, borderTopColor: '#eee' },
  placeBtn: { backgroundColor: '#000', padding: 15, borderRadius: 8, alignItems: 'center' },
  placeBtnDisabled: { backgroundColor: '#666' },
  placeBtnText: { color: '#fff', fontSize: 18, fontWeight: 'bold' },
});
