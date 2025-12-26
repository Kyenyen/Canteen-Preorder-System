<template>
  <!-- Modal Backdrop -->
  <div v-if="isOpen && product" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-end sm:items-center justify-center fade-in backdrop-blur-sm">
    
    <!-- Modal Content -->
    <!-- Added @click.stop to prevent closing when clicking inside the modal -->
    <div class="bg-white dark:bg-gray-800 w-full sm:max-w-lg sm:rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden transform transition-transform duration-300 scale-100 flex flex-col max-h-[90vh]">
        
        <!-- Close Button -->
        <button @click="closeModal" class="absolute top-4 right-4 z-10 bg-white/80 dark:bg-black/50 backdrop-blur p-2 rounded-full text-gray-500 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-white dark:hover:bg-gray-700 transition focus:outline-none">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Product Image Header -->
        <div class="h-64 flex items-center justify-center relative shrink-0 bg-gray-100 dark:bg-gray-700 overflow-hidden">
            
            <!-- Product Photo or Icon -->
            <div v-if="product.photoUrl" class="w-full h-full">
                <img :src="product.photoUrl" class="w-full h-full object-cover" :alt="product.name">
            </div>
            <i v-else :class="[product.icon, product.textColor || 'text-orange-500']" class="text-8xl relative z-10 drop-shadow-md"></i>
            
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
        </div>

        <!-- Content (Scrollable) -->
        <div class="p-6 sm:p-8 overflow-y-auto">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="text-xs font-bold text-orange-600 bg-orange-100 dark:text-orange-300 dark:bg-orange-900/30 px-2 py-1 rounded uppercase tracking-wider">
                        {{ product.category }}
                    </span>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2 leading-tight">
                        {{ product.name }}
                    </h2>
                </div>
                <span class="text-2xl font-extrabold text-gray-900 dark:text-white whitespace-nowrap">
                    RM {{ formatPrice(product.price) }}
                </span>
            </div>

            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-6">
                {{ product.description || 'No description available for this item.' }}
            </p>

            <!-- Quantity Selector -->
            <div class="flex items-center justify-between mb-8 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                <span class="font-bold text-gray-700 dark:text-gray-200 text-sm pl-2">Quantity</span>
                <div class="flex items-center gap-4">
                    <button @click="updateQty(-1)" :disabled="quantity <= 1" class="w-8 h-8 rounded-full bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 flex items-center justify-center text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-500 active:scale-95 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="font-bold text-lg w-6 text-center dark:text-white select-none">{{ quantity }}</span>
                    <button @click="updateQty(1)" class="w-8 h-8 rounded-full bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 flex items-center justify-center text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-500 active:scale-95 transition shadow-sm">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Action Footer -->
            <button @click="addToCart" class="w-full bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 text-lg flex items-center justify-center gap-3">
                <span>Add to Cart - RM {{ formatPrice(totalPrice) }}</span>
            </button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    isOpen: Boolean,
    product: Object // Expected format: { id, name, price, description, category, icon, bgColor?, textColor? }
})

const emit = defineEmits(['close', 'add-to-cart'])

const quantity = ref(1)

// Reset quantity when modal opens or product changes
watch(() => props.product, () => {
    quantity.value = 1
})

// Computed Total Price
const totalPrice = computed(() => {
    return props.product ? (props.product.price * quantity.value) : 0
})

// Format Price
const formatPrice = (value) => {
    return Number(value).toFixed(2)
}

// Update Quantity
const updateQty = (change) => {
    const newQty = quantity.value + change
    if (newQty >= 1) {
        quantity.value = newQty
    }
}

// Close Modal
const closeModal = () => {
    emit('close')
}

// Add To Cart
const addToCart = () => {
    emit('add-to-cart', {
        product: props.product,
        quantity: quantity.value,
        totalPrice: totalPrice.value
    })
    closeModal()
}
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.2s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0 }
  to { opacity: 1 }
}
</style>