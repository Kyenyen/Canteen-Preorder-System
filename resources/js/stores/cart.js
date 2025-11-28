import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCartStore = defineStore('cart', () => {
    // State
    const items = ref([]); // array of { id, name, price, qty }

    // Getters
    const totalItems = computed(() => items.value.reduce((sum, item) => sum + item.qty, 0));
    const subtotal = computed(() => items.value.reduce((sum, item) => sum + item.price * item.qty, 0));

    // Actions
    function addItem(product, quantity = 1) {
        const existing = items.value.find(i => i.id === product.id);
        if (existing) {
            existing.qty += quantity;
        } else {
            items.value.push({ ...product, qty: quantity });
        }
    }

    function removeItem(productId) {
        items.value = items.value.filter(item => item.id !== productId);
    }

    function updateQuantity(productId, qty) {
        const item = items.value.find(i => i.id === productId);
        if (item) {
            item.qty = qty;
        }
    }

    function clearCart() {
        items.value = [];
    }

    return { items, totalItems, subtotal, addItem, removeItem, updateQuantity, clearCart };
});
