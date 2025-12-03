<template>
  <div class="flex flex-col min-h-screen fade-in">
    <!-- Menu Grid -->
    <div class="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-gray-900 w-full transition-colors duration-300">
      
      <!-- Categories (Dynamic) -->
      <div class="flex gap-3 mb-6 overflow-x-auto pb-2 scrollbar-hide">
        <button
          v-for="cat in categories"
          :key="cat"
          @click="filterMenu(cat)"
          :class="['px-6 py-2 rounded-full text-sm font-bold shadow-md transition-all transform hover:scale-105 whitespace-nowrap',
                   currentCategory === cat ? 'bg-gray-900 dark:bg-gray-700 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700']"
        >
          {{ cat }}
        </button>
      </div>

      <!-- Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 pb-20">
        
        <!-- Menu Item Card -->
        <div v-for="item in filteredMenu" :key="item.product_id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col hover:shadow-md transition">
          
          <!-- Image - Clickable to view details -->
          <div 
            @click="openProductDetails(item)"
            class="h-48 w-full bg-gray-100 dark:bg-gray-700 relative cursor-pointer group"
          >
             <img v-if="item.photo" :src="getPhotoUrl(item.photo)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
             <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                <i class="fas fa-utensils text-4xl"></i>
             </div>
             <!-- Category Badge (Fixed JSON Display) -->
             <span class="absolute top-3 left-3 bg-white/90 dark:bg-black/60 backdrop-blur text-xs font-bold px-2 py-1 rounded-md text-gray-800 dark:text-white uppercase tracking-wide">
                {{ typeof item.category === 'object' && item.category ? item.category.name : item.category }}
             </span>
             <!-- View Details Overlay -->
             <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
               <span class="text-white opacity-0 group-hover:opacity-100 transition-opacity font-semibold text-sm">
                 <i class="fas fa-eye mr-2"></i>View Details
               </span>
             </div>
          </div>

          <!-- Content -->
          <div class="p-4 flex flex-col flex-1">
            <div class="flex-1">
                <div class="flex justify-between items-start mb-1">
                    <h4 
                      @click="openProductDetails(item)"
                      class="font-bold text-gray-800 dark:text-white text-lg leading-tight cursor-pointer hover:text-orange-600 dark:hover:text-orange-400 transition"
                    >
                      {{ item.name }}
                    </h4>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ item.description }}</p>
            </div>
            
            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
              <span class="text-orange-600 dark:text-orange-400 font-extrabold text-lg">RM {{ Number(item.price).toFixed(2) }}</span>
              
              <!-- Add Button / Quantity Selector Container -->
              <div class="relative min-w-[120px] flex justify-end">
                <!-- Add Button (shows when not in cart) -->
                <Transition
                  enter-active-class="transition-all duration-300 ease-out"
                  enter-from-class="opacity-0 scale-75"
                  enter-to-class="opacity-100 scale-100"
                  leave-active-class="transition-all duration-200 ease-in"
                  leave-from-class="opacity-100 scale-100"
                  leave-to-class="opacity-0 scale-75"
                  mode="out-in"
                >
                  <button 
                    v-if="!isInCart(item.product_id)"
                    :key="'add-' + item.product_id"
                    @click="addToCart(item)" 
                    class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg shadow-orange-200 dark:shadow-none transition-all transform hover:scale-105 active:scale-95 flex items-center gap-2"
                  >
                    <i class="fas fa-cart-plus"></i> Add
                  </button>
                  
                  <!-- Quantity Selector (shows when in cart) -->
                  <div 
                    v-else 
                    :key="'qty-' + item.product_id"
                    class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden"
                  >
                    <button 
                      @click="decrementQty(item.product_id)" 
                      class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-orange-100 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 transition-all transform active:scale-90"
                    >
                      <i class="fas fa-minus text-sm"></i>
                    </button>
                    <span class="px-4 py-2 text-gray-800 dark:text-white font-semibold min-w-[3rem] text-center quantity-display">
                      {{ getCartQuantity(item.product_id) }}
                    </span>
                    <button 
                      @click="incrementQty(item.product_id)" 
                      class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-orange-100 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 transition-all transform active:scale-90"
                    >
                      <i class="fas fa-plus text-sm"></i>
                    </button>
                  </div>
                </Transition>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredMenu.length === 0" class="col-span-full flex flex-col items-center justify-center py-20 text-gray-400 dark:text-gray-500">
          <i class="fas fa-hamburger text-6xl mb-4 opacity-20"></i>
          <p>No items found in this category.</p>
        </div>

      </div>
    </div>

    <!-- Notification Component -->
    <Notification
      :show="notification.show"
      :type="notification.type"
      :title="notification.title"
      :message="notification.message"
      @close="notification.show = false"
    />

    <!-- Product Details Modal -->
    <ProductModal
      :isOpen="isProductModalOpen"
      :product="selectedProduct"
      @close="closeProductDetails"
      @add-to-tray="handleAddFromModal"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useCartStore } from '../../../js/stores/cart'
import axios from 'axios'
import Notification from '../components/Notification.vue'
import ProductModal from '../components/Product-modal.vue'

const currentCategory = ref('All')
const menuItems = ref([])
const pendingUpdates = ref({}) // Track pending quantity updates
const updateTimeouts = ref({}) // Track debounce timeouts
const isProductModalOpen = ref(false)
const selectedProduct = ref(null)

const cartStore = useCartStore()
const toggleCart = inject('toggleCart')

// Use the global notification state from App.vue
const notification = inject('notificationState')

const fetchMenu = async () => {
  try {
    const response = await axios.get('/api/menu')
    menuItems.value = response.data
  } catch (err) {
    console.error('Failed to fetch menu', err)
  }
}

onMounted(fetchMenu)

// Dynamic Categories Calculation
const categories = computed(() => {
    const cats = new Set(['All'])
    menuItems.value.forEach(item => {
        if (item.category) {
            // Handle if category is object (from backend relationship) or string
            const name = typeof item.category === 'object' ? item.category.name : item.category
            cats.add(name)
        }
    })
    return Array.from(cats)
})

const filteredMenu = computed(() => {
    if (currentCategory.value === 'All') {
        return menuItems.value
    }
    return menuItems.value.filter(item => {
        const catName = typeof item.category === 'object' ? item.category.name : item.category
        return catName === currentCategory.value
    })
})

const filterMenu = (cat) => {
    currentCategory.value = cat
}

const isInCart = (productId) => {
  return cartStore.items.some(item => item.id === productId)
}

const getCartQuantity = (productId) => {
  // Check if there's a pending update first
  if (pendingUpdates.value[productId] !== undefined) {
    return pendingUpdates.value[productId]
  }
  const cartItem = cartStore.items.find(item => item.id === productId)
  return cartItem ? cartItem.qty : 0
}

const updateQuantityDebounced = (productId, newQty) => {
  // Clear existing timeout for this product
  if (updateTimeouts.value[productId]) {
    clearTimeout(updateTimeouts.value[productId])
  }

  // Update cart store immediately for real-time UI sync
  const item = cartStore.items.find(i => i.id === productId)
  if (item) {
    const previousQty = item.qty
    item.qty = newQty // Update cart store immediately
    pendingUpdates.value[productId] = newQty // Also track pending update

    // Debounce the actual API call
    updateTimeouts.value[productId] = setTimeout(async () => {
      try {
        await cartStore.updateQuantity(productId, newQty)
        // Clear pending update after successful update
        delete pendingUpdates.value[productId]
      } catch (error) {
        console.error('Failed to update quantity:', error)
        // Revert both on error
        if (item) {
          item.qty = previousQty
        }
        delete pendingUpdates.value[productId]
        showNotification('error', 'Error', 'Failed to update quantity')
      }
    }, 300) // Wait 300ms after last click before sending API request
  }
}

const incrementQty = async (productId) => {
  const currentQty = getCartQuantity(productId)
  const newQty = currentQty + 1
  
  // Add bounce animation to quantity
  const qtyElement = document.querySelector(`[key="qty-${productId}"] .quantity-display`)
  if (qtyElement) {
    qtyElement.style.animation = 'quantityBounce 0.3s ease-out'
    setTimeout(() => qtyElement.style.animation = '', 300)
  }
  
  // Update immediately with debounce
  updateQuantityDebounced(productId, newQty)
}

const decrementQty = async (productId) => {
  const currentQty = getCartQuantity(productId)
  
  if (currentQty <= 1) {
    // Clear any pending updates
    if (updateTimeouts.value[productId]) {
      clearTimeout(updateTimeouts.value[productId])
      delete updateTimeouts.value[productId]
      delete pendingUpdates.value[productId]
    }
    
    // Remove from cart when clicking minus at quantity 1
    try {
      await cartStore.removeItem(productId)
      showNotification('info', 'Removed', 'Item removed from cart')
    } catch (error) {
      console.error('Failed to remove item:', error)
      showNotification('error', 'Error', 'Failed to remove item')
    }
  } else {
    // Decrease quantity
    const newQty = currentQty - 1
    
    // Add bounce animation to quantity
    const qtyElement = document.querySelector(`[key="qty-${productId}"] .quantity-display`)
    if (qtyElement) {
      qtyElement.style.animation = 'quantityBounce 0.3s ease-out'
      setTimeout(() => qtyElement.style.animation = '', 300)
    }
    
    // Update immediately with debounce
    updateQuantityDebounced(productId, newQty)
  }
}

const showNotification = (type, title, message = '') => {
  notification.value = {
    show: true,
    type,
    title,
    message
  }
}

const addToCart = async (item) => {
  try {
    await cartStore.addItem(item, 1)
    showNotification('success', 'Added to cart!', `${item.name} added successfully`)
  } catch (error) {
    console.error('Failed to add item to cart:', error)
    const errorMsg = error.response?.data?.message || 'Failed to add item to cart'
    showNotification('error', 'Error', errorMsg)
  }
}

const getPhotoUrl = (path) => {
    if (!path) return null
    return path.startsWith('http') ? path : `/${path}`
}

const openProductDetails = (item) => {
  selectedProduct.value = {
    id: item.product_id,
    name: item.name,
    price: item.price,
    description: item.description,
    category: typeof item.category === 'object' ? item.category.name : item.category,
    photo: item.photo,
    photoUrl: getPhotoUrl(item.photo),
    icon: 'fas fa-utensils',
    bgColor: 'bg-orange-50 dark:bg-gray-700',
    textColor: 'text-orange-500'
  }
  isProductModalOpen.value = true
}

const closeProductDetails = () => {
  isProductModalOpen.value = false
  selectedProduct.value = null
}

const handleAddFromModal = async (data) => {
  try {
    const item = menuItems.value.find(i => i.product_id === data.product.id)
    if (item) {
      await cartStore.addItem(item, data.quantity)
      showNotification('success', 'Added to cart!', `${data.product.name} (x${data.quantity}) added successfully`)
    }
  } catch (error) {
    console.error('Failed to add item to cart:', error)
    const errorMsg = error.response?.data?.message || 'Failed to add item to cart'
    showNotification('error', 'Error', errorMsg)
  }
}
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0 }
  to { opacity: 1 }
}

/* Quantity number animation */
.quantity-display {
  transition: transform 0.15s ease-out;
}

@keyframes quantityBounce {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.15); }
}

/* Hide scrollbar for category list */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>