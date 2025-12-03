import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useCartStore = defineStore('cart', () => {
    // State
    const items = ref([]);
    const loading = ref(false);

    // Getters
    const totalItems = computed(() => items.value.reduce((sum, item) => sum + item.qty, 0));
    const subtotal = computed(() => items.value.reduce((sum, item) => sum + item.price * item.qty, 0));

    // Helper to ensure CSRF cookie
    const ensureCsrfCookie = async () => {
        await axios.get('/sanctum/csrf-cookie');
    };

    // Actions
    async function fetchCart() {
        loading.value = true;
        try {
            const response = await axios.get('/api/cart');
            items.value = response.data.items || [];
            console.log('Cart fetched:', items.value);
        } catch (error) {
            console.error('Error fetching cart:', error);
            items.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function addItem(product, quantity = 1) {
        try {
            await ensureCsrfCookie();
            const productId = product.product_id || product.id;
            console.log('Adding to cart:', productId, quantity);
            await axios.post('/api/cart', {
                product_id: productId,
                quantity: quantity
            });
            await fetchCart(); // Refresh cart from server
            console.log('Item added, cart now has:', items.value.length, 'items');
            return true;
        } catch (error) {
            console.error('Error adding to cart:', error);
            console.error('Error details:', error.response?.data);
            throw error;
        }
    }

    async function removeItem(productId) {
        // Optimistic update - remove from UI immediately
        const itemIndex = items.value.findIndex(i => i.id === productId);
        let removedItem = null;
        
        if (itemIndex !== -1) {
            removedItem = items.value.splice(itemIndex, 1)[0]; // Remove immediately
            
            try {
                await axios.delete(`/api/cart/${productId}`);
                // Successfully removed
            } catch (error) {
                // Revert on error - add it back
                if (removedItem) {
                    items.value.splice(itemIndex, 0, removedItem);
                }
                console.error('Error removing from cart:', error);
                throw error;
            }
        }
    }

    async function updateQuantity(productId, qty) {
        if (qty < 1) return;
        
        const item = items.value.find(i => i.id === productId);
        if (item) {
            // Store previous quantity in case we need to revert
            const previousQty = item.qty;
            
            // Only update if not already at this quantity (prevents double updates)
            if (item.qty !== qty) {
                item.qty = qty;
            }
            
            try {
                await axios.put(`/api/cart/${productId}`, { quantity: qty });
                // Successfully updated
            } catch (error) {
                // Revert on error
                item.qty = previousQty;
                console.error('Error updating quantity:', error);
                throw error;
            }
        }
    }

    async function clearCart() {
        try {
            await axios.delete('/api/cart');
            items.value = [];
        } catch (error) {
            console.error('Error clearing cart:', error);
        }
    }

    return { 
        items, 
        loading,
        totalItems, 
        subtotal, 
        fetchCart,
        addItem, 
        removeItem, 
        updateQuantity, 
        clearCart 
    };
});
