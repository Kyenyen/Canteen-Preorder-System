<template>
  <div class="flex flex-col min-h-screen fade-in">
    <!-- Menu Grid -->
    <div class="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-gray-900 w-full transition-colors duration-300">
      
      <!-- Categories -->
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
          
          <!-- Image -->
          <div class="h-48 w-full bg-gray-100 dark:bg-gray-700 relative">
             <img v-if="item.photo" :src="getPhotoUrl(item.photo)" class="w-full h-full object-cover">
             <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                <i class="fas fa-utensils text-4xl"></i>
             </div>
             <!-- Category Badge -->
             <span class="absolute top-3 left-3 bg-white/90 dark:bg-black/60 backdrop-blur text-xs font-bold px-2 py-1 rounded-md text-gray-800 dark:text-white uppercase tracking-wide">
                {{ item.category }}
             </span>
          </div>

          <!-- Content -->
          <div class="p-4 flex flex-col flex-1">
            <div class="flex-1">
                <div class="flex justify-between items-start mb-1">
                    <h4 class="font-bold text-gray-800 dark:text-white text-lg leading-tight">{{ item.name }}</h4>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ item.description }}</p>
            </div>
            
            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
              <span class="text-orange-600 dark:text-orange-400 font-extrabold text-lg">RM {{ Number(item.price).toFixed(2) }}</span>
              <button @click="addToCart(item)" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-orange-200 dark:shadow-none transition transform active:scale-95 flex items-center gap-2">
                <i class="fas fa-plus"></i> Add
              </button>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '../../../js/stores/cart'
import axios from 'axios'

const categories = ['All', 'Breakfast', 'Lunch', 'Beverage']
const currentCategory = ref('All')
const menuItems = ref([])

const cartStore = useCartStore()

const fetchMenu = async () => {
  try {
    const response = await axios.get('/api/menu')
    menuItems.value = response.data
  } catch (err) {
    console.error('Failed to fetch menu', err)
  }
}

onMounted(fetchMenu)

const filteredMenu = computed(() => {
  if (currentCategory.value === 'All') return menuItems.value
  return menuItems.value.filter(item => item.category === currentCategory.value)
})

const filterMenu = (cat) => {
  currentCategory.value = cat
}

const addToCart = (item) => {
  cartStore.addItem(item)
}

const getPhotoUrl = (path) => {
    if (!path) return null
    return path.startsWith('http') ? path : `/${path}`
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
/* Hide scrollbar for category list */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>