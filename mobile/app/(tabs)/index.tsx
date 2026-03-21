import React, { useState } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, Image, ActivityIndicator } from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { fetchCategories, fetchMenuItems, Category, MenuItem } from '@/services/menu';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';

export default function MenuScreen() {
  const [selectedCategory, setSelectedCategory] = useState<number | null>(null);
  const router = useRouter();

  const { data: categories, isLoading: isCatsLoading } = useQuery({
    queryKey: ['categories'],
    queryFn: fetchCategories,
  });

  const { data: menuMap, isLoading: isMenuLoading } = useQuery({
    queryKey: ['menuItems'],
    queryFn: fetchMenuItems,
  });

  const isLoading = isCatsLoading || isMenuLoading;

  if (isLoading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color="#000" />
      </View>
    );
  }

  // Determine active category
  const activeCategoryId = selectedCategory || (categories && categories.length > 0 ? categories[0].id : null);
  
  // Get items for active category
  const activeItems = activeCategoryId && menuMap ? menuMap[activeCategoryId.toString()] || [] : [];

  const renderCategory = ({ item }: { item: Category }) => {
    const isActive = item.id === activeCategoryId;
    return (
      <TouchableOpacity 
        style={[styles.categoryPill, isActive && styles.activeCategoryPill]} 
        onPress={() => setSelectedCategory(item.id)}
      >
        <Text style={[styles.categoryText, isActive && styles.activeCategoryText]}>
          {item.name}
        </Text>
      </TouchableOpacity>
    );
  };

  const renderMenuItem = ({ item }: { item: MenuItem }) => (
    <TouchableOpacity 
      style={styles.menuCard} 
      onPress={() => router.push(`/menu/${item.id}` as any)}
    >
      <View style={styles.menuInfo}>
        <Text style={styles.menuName}>{item.name}</Text>
        {item.description ? <Text style={styles.menuDesc} numberOfLines={2}>{item.description}</Text> : null}
        <Text style={styles.menuPrice}>฿{Number(item.price).toFixed(2)}</Text>
      </View>
      <View style={styles.menuImageContainer}>
        {item.image_url ? (
          <Image source={{ uri: item.image_url }} style={styles.menuImage} />
        ) : (
          <View style={styles.placeholderImage} />
        )}
      </View>
    </TouchableOpacity>
  );

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      <Text style={styles.headerTitle}>Available Options</Text>
      
      <View style={styles.categoriesWrapper}>
        <FlatList
          data={categories}
          renderItem={renderCategory}
          keyExtractor={(item) => item.id.toString()}
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.categoriesContainer}
        />
      </View>

      <FlatList
        data={activeItems}
        renderItem={renderMenuItem}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.menuList}
        ListEmptyComponent={<Text style={styles.emptyText}>No items available.</Text>}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f9f9f9' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  headerTitle: { fontSize: 24, fontWeight: 'bold', padding: 20, paddingBottom: 10 },
  categoriesWrapper: { paddingBottom: 15, borderBottomWidth: 1, borderBottomColor: '#eee' },
  categoriesContainer: { paddingHorizontal: 15, gap: 10 },
  categoryPill: { paddingHorizontal: 20, paddingVertical: 10, borderRadius: 20, backgroundColor: '#fff', elevation: 2, shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.1, shadowRadius: 2 },
  activeCategoryPill: { backgroundColor: '#000' },
  categoryText: { fontWeight: '600', color: '#666' },
  activeCategoryText: { color: '#fff' },
  menuList: { padding: 15, gap: 15 },
  emptyText: { textAlign: 'center', marginTop: 40, color: '#999' },
  menuCard: { flexDirection: 'row', backgroundColor: '#fff', borderRadius: 12, padding: 15, elevation: 2, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4 },
  menuInfo: { flex: 1, paddingRight: 10, justifyContent: 'center' },
  menuName: { fontSize: 16, fontWeight: 'bold', marginBottom: 4 },
  menuDesc: { fontSize: 14, color: '#666', marginBottom: 8 },
  menuPrice: { fontSize: 15, fontWeight: 'bold', color: '#000' },
  menuImageContainer: { width: 90, height: 90, borderRadius: 8, overflow: 'hidden' },
  menuImage: { width: '100%', height: '100%', resizeMode: 'cover' },
  placeholderImage: { width: '100%', height: '100%', backgroundColor: '#eee' }
});
