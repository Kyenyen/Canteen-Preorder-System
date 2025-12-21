<template>
  <div class="h-full flex-col fade-in bg-gray-50 dark:bg-gray-900 overflow-y-auto transition-colors duration-300">
    <!-- Header/Greeting -->
    <div class="bg-white dark:bg-gray-800 px-6 py-8 border-b border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-300">
      <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white mb-1">{{ greetingMessage }}</h2>
        <p class="text-gray-500 dark:text-gray-400">
            {{ isAdmin ? 'Ready to manage the kitchen?' : 'What would you like to eat today?' }}
        </p>
      </div>
    </div>

    <div class="p-6 max-w-4xl mx-auto w-full space-y-8 pb-20">
      
      <!-- Active Order Status Widget (User Only) -->
      <div
        v-if="activeOrder && !isAdmin"
        class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border-l-8 border-orange-500 cursor-pointer hover:shadow-xl transition transform hover:-translate-y-1"
        @click="$router.push('/history')"
      >
        <div class="flex justify-between items-center">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="h-2 w-2 bg-green-500 rounded-full animate-pulse"></span>
              <p class="text-xs font-bold text-orange-500 dark:text-orange-400 uppercase tracking-wider">Active Order</p>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Order #{{ activeOrder.order_id }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Status: {{ activeOrder.status }}</p>
          </div>
          <div class="h-14 w-14 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-orange-600 dark:text-orange-400 text-2xl">
            <i class="fas fa-utensils"></i>
          </div>
        </div>
      </div>

      <!-- Hero/Promo -->
      <div class="relative rounded-2xl overflow-hidden bg-gray-900 dark:bg-black text-white shadow-xl group">
        <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 dark:from-orange-800 dark:to-red-900 opacity-90 group-hover:opacity-100 transition duration-500"></div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
        <div class="absolute left-10 bottom-10 w-20 h-20 bg-yellow-300 opacity-20 rounded-full blur-xl"></div>

        <div class="relative p-8 sm:p-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
          <div>
            <span class="bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3 inline-block border border-white/30">Promo of the Day</span>
            <h3 class="text-3xl font-extrabold mb-2">{{ promo.title }}</h3>
            <p class="text-orange-100 mb-6 max-w-md text-sm leading-relaxed">{{ promo.description }}</p>
            
            <button
              v-if="!isAdmin"
              @click="$router.push('/menu')"
              class="bg-white text-orange-600 font-bold py-3 px-8 rounded-xl shadow-lg hover:bg-gray-50 transition transform active:scale-95 flex items-center gap-2"
            >
              Order Now <i class="fas fa-arrow-right text-xs"></i>
            </button>
          </div>
          <i class="fas fa-bowl-rice text-9xl opacity-30 rotate-12 transform translate-x-4 translate-y-4 sm:block hidden"></i>
        </div>
      </div>

      <!-- Quick Shortcuts -->
      <div class="grid gap-4" :class="isAdmin ? 'grid-cols-1' : 'grid-cols-2'">
        
        <!-- Menu Button (Dynamic for Admin/User) -->
        <div
          @click="goToMenu"
          class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-orange-200 dark:hover:border-orange-700 hover:shadow-md transition cursor-pointer group"
        >
          <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-4 group-hover:scale-110 transition duration-300">
            <i class="fas fa-book-open text-xl"></i>
          </div>
          <h4 class="font-bold text-gray-800 dark:text-white text-lg">{{ isAdmin ? 'Manage Menu' : 'Full Menu' }}</h4>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ isAdmin ? 'Edit items & prices' : 'Browse all available items' }}</p>
        </div>

        <!-- Order History (Hidden for Admin) -->
        <div
          v-if="!isAdmin"
          @click="$router.push('/history')"
          class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-orange-200 dark:hover:border-orange-700 hover:shadow-md transition cursor-pointer group"
        >
          <div class="h-12 w-12 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400 mb-4 group-hover:scale-110 transition duration-300">
            <i class="fas fa-clock-rotate-left text-xl"></i>
          </div>
          <h4 class="font-bold text-gray-800 dark:text-white text-lg">Order History</h4>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Track or re-order</p>
        </div>
      </div>

      <!-- Popular Section -->
      <div>
        <h3 class="font-bold text-gray-800 dark:text-white text-lg mb-4 flex items-center gap-2">
          <i class="fas fa-star text-yellow-400"></i> Popular Today
        </h3>
        <div v-if="popularItems.length > 0" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div v-for="item in popularItems" :key="item.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col justify-between">
            <div>
                <h4 class="font-bold text-gray-800 dark:text-white">{{ item.name }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mt-1">{{ item.description }}</p>
            </div>
            <p class="text-orange-600 dark:text-orange-400 font-bold mt-3">RM {{ Number(item.price).toFixed(2) }}</p>
          </div>
        </div>
        <div v-else class="text-gray-500 text-sm italic">
            No popular items to display yet.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../../js/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Check role
const isAdmin = computed(() => authStore.user?.role === 'admin')
const greetingMessage = computed(() => `Welcome back, ${authStore.user?.username || 'User'}!`)

// Initial State
const activeOrder = ref(null)
const promo = ref({
  title: 'Loading Promo...',
  description: "Please wait while we fetch the latest deals."
})
const popularItems = ref([])
let pollingInterval = null

const goToMenu = () => {
    if (isAdmin.value) {
        router.push('/admin/menu')
    } else {
        router.push('/menu')
    }
}

const fetchHomeData = async () => {
  try {
    const response = await axios.get('/api/home-data')
    
    // 1. Update Active Order (clear if not returned)
    if (response.data.activeOrder) {
        activeOrder.value = response.data.activeOrder
    } else {
        activeOrder.value = null
    }

    // 2. Update Popular Items
    if (response.data.popularItems) {
        popularItems.value = response.data.popularItems
    }

    // 3. Update Promo (Fix for "didn't change")
    if (response.data.promo) {
        promo.value = response.data.promo
    }

  } catch (err) {
    console.error('Failed to fetch home data', err)
    // Fallback if API fails
    promo.value = {
        title: 'UniCanteen Special',
        description: "Enjoy delicious meals at student-friendly prices!"
    }
  }
}

onMounted(() => {
  fetchHomeData()
  
  // Poll for active order updates every 5 seconds (only for non-admin users)
  if (!isAdmin.value) {
    pollingInterval = setInterval(() => {
      fetchHomeData()
    }, 5000)
  }
})

onUnmounted(() => {
  // Clear polling interval when component is unmounted
  if (pollingInterval) {
    clearInterval(pollingInterval)
  }
})
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