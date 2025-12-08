<template>
  <!-- VIEW: HISTORY (McDonald's Style) -->
  <div id="view-history" class="h-full flex-col fade-in bg-gray-100 dark:bg-gray-900 overflow-y-auto transition-colors duration-300">
    <div class="max-w-3xl mx-auto w-full p-6 pb-20">
      <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white mb-6">My Orders</h2>

      <!-- Active Orders Section -->
      <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4 tracking-wide">In Progress</h3>
      <div id="active-orders-container" class="space-y-6 mb-10">
        <div v-if="activeOrders.length === 0" class="text-gray-400 dark:text-gray-500 text-center py-6">
          No active orders.
        </div>
        <div v-for="order in activeOrders" :key="order.order_id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border-l-4 border-orange-500 overflow-hidden">
          <!-- Order Header -->
          <div class="p-6 pb-4">
            <div class="flex justify-between items-start mb-6">
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Order Number</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ order.order_id }}</h3>
              </div>
              <div class="text-right">
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ order.pickup_time }}</p>
              </div>
            </div>

            <!-- Progress Steps -->
            <div class="flex items-center justify-between mb-6">
              <!-- Placed -->
              <div class="flex flex-col items-center flex-1">
                <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white mb-2">
                  <i class="fas fa-check text-lg"></i>
                </div>
                <p class="text-xs font-semibold text-green-600 dark:text-green-400">Placed</p>
              </div>

              <!-- Progress Line -->
              <div class="flex-1 h-1 mx-2" :class="order.status === 'Preparing' || order.status === 'Ready' ? 'bg-green-500' : 'bg-gray-300'"></div>

              <!-- Prep -->
              <div class="flex flex-col items-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white mb-2" :class="[
                  order.status === 'Preparing' || order.status === 'Ready' ? 'bg-green-500' : 'bg-gray-300',
                  order.status === 'Preparing' ? 'animate-pulse' : ''
                ]">
                  <i class="fas fa-utensils text-lg"></i>
                </div>
                <p class="text-xs font-semibold" :class="order.status === 'Preparing' || order.status === 'Ready' ? 'text-green-600 dark:text-green-400' : 'text-gray-500'">Prep</p>
              </div>

              <!-- Progress Line -->
              <div class="flex-1 h-1 mx-2" :class="order.status === 'Ready' ? 'bg-green-500' : 'bg-gray-300'"></div>

              <!-- Ready -->
              <div class="flex flex-col items-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white mb-2" :class="order.status === 'Ready' ? 'bg-green-500' : 'bg-gray-300'">
                  <i class="fas fa-shopping-bag text-lg"></i>
                </div>
                <p class="text-xs font-semibold" :class="order.status === 'Ready' ? 'text-green-600 dark:text-green-400' : 'text-gray-500'">Ready</p>
              </div>
            </div>

            <!-- Order Items -->
            <div class="space-y-2 mb-4">
              <p v-for="item in order.products" :key="item.product_id" class="text-gray-700 dark:text-gray-300">
                {{ item.pivot.quantity }}x {{ item.name }}
              </p>
            </div>

            <!-- Total -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
              <span class="text-gray-700 dark:text-gray-300 font-medium">Total</span>
              <span class="text-2xl font-bold text-gray-900 dark:text-white">RM {{ parseFloat(order.total).toFixed(2) }}</span>
            </div>
          </div>

          <!-- Cancel Button -->
          <div class="px-6 pb-6">
            <button 
              @click="cancelOrder(order.order_id)" 
              :disabled="cancelling === order.order_id"
              class="w-full py-3 border-2 border-red-500 text-red-500 font-semibold rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
              {{ cancelling === order.order_id ? 'Cancelling...' : 'Cancel Order' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Past Orders Section -->
      <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4 tracking-wide">Past Orders</h3>
      <div id="past-orders-container" class="space-y-4">
        <div v-if="pastOrders.length === 0" class="text-gray-400 dark:text-gray-500 text-center py-6">
          No past orders.
        </div>
        <div v-for="order in pastOrders" :key="order.order_id" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
          <div class="flex justify-between items-start mb-3">
            <div>
              <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ order.order_id }}</h4>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ order.date }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase" :class="{
              'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': order.status === 'Completed',
              'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': order.status === 'Cancelled'
            }">
              {{ order.status }}
            </span>
          </div>

          <!-- Order Items -->
          <div class="space-y-1 mb-3">
            <p v-for="item in order.products" :key="item.product_id" class="text-sm text-gray-600 dark:text-gray-400">
              {{ item.pivot.quantity }}x {{ item.name }}
            </p>
          </div>

          <!-- Footer -->
          <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
            <span class="text-gray-500 dark:text-gray-400 text-sm">{{ order.pickup_time }}</span>
            <button 
              @click="reorder(order.order_id)"
              class="px-4 py-2 border-2 border-orange-500 text-orange-500 font-semibold rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors flex items-center gap-2">
              <i class="fas fa-redo"></i> Order Again
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useCartStore } from '../../../js/stores/cart'
import { useRouter } from 'vue-router'

const orders = ref([])
const cancelling = ref(null)
const cartStore = useCartStore()
const router = useRouter()

const activeOrders = computed(() => {
  return orders.value.filter(o => o.status === 'Preparing' || o.status === 'Ready')
})

const pastOrders = computed(() => {
  return orders.value.filter(o => o.status === 'Completed' || o.status === 'Cancelled')
})

const fetchOrders = async () => {
  try {
    const response = await axios.get('/api/orders/history')
    orders.value = response.data
    console.log('Orders fetched:', response.data)
  } catch (err) {
    console.error('Failed to fetch orders', err)
  }
}

const cancelOrder = async (orderId) => {
  if (!confirm('Are you sure you want to cancel this order?')) {
    return
  }

  cancelling.value = orderId
  try {
    await axios.put(`/api/orders/${orderId}/cancel`)
    // Refresh orders after cancellation
    await fetchOrders()
    alert('Order cancelled successfully')
  } catch (err) {
    console.error('Failed to cancel order', err)
    alert(err.response?.data?.message || 'Failed to cancel order')
  } finally {
    cancelling.value = null
  }
}

const reorder = async (orderId) => {
  try {
    const order = orders.value.find(o => o.order_id === orderId)
    if (!order || !order.products) return

    // Add all items from the order to cart
    for (const item of order.products) {
      await cartStore.addItem(item, item.pivot.quantity)
    }
    
    alert(`${order.products.length} item(s) added to cart!`)
    // Navigate to menu page
    router.push('/menu')
  } catch (err) {
    console.error('Failed to reorder', err)
    alert('Failed to add items to cart. Please try again.')
  }
}

onMounted(fetchOrders)
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0 }
  to { opacity: 1 }
}

/* Pulse animation is built into Tailwind CSS via animate-pulse class */
</style>
