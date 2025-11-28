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
          :class="['category-btn px-6 py-2 rounded-full text-sm font-bold shadow-md transition-all transform hover:scale-105',
                    currentCategory === cat ? 'bg-gray-900 dark:bg-gray-700 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700']"
        >
          {{ cat }}
        </button>
      </div>

      <!-- Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 pb-20">
        <div v-for="item in filteredMenu" :key="item.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col justify-between">
          <div>
            <h4 class="font-bold text-gray-800 dark:text-white mb-2">{{ item.name }}</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ item.description }}</p>
          </div>
          <div class="flex justify-between items-center mt-2">
            <span class="text-orange-600 dark:text-orange-400 font-bold">RM {{ item.price.toFixed(2) }}</span>
            <button @click="addToCart(item)" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded-xl text-sm transition transform active:scale-95">
              Add
            </button>
          </div>
        </div>
        <div v-if="filteredMenu.length === 0" class="col-span-full text-center text-gray-400 dark:text-gray-500 py-10">
          No items found.
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
    const response = await axios.get('/api/menu') // replace with your API
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
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0 }
  to { opacity: 1 }
}
</style>
