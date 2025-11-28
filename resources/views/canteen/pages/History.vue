<template>
  <div class="flex flex-col min-h-screen fade-in bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-3xl mx-auto w-full p-6 pb-20">
      <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white mb-6">My Orders</h2>

      <!-- Active Orders Section -->
      <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4 tracking-wide">In Progress</h3>
      <div class="space-y-6 mb-10">
        <div v-if="activeOrders.length === 0" class="text-gray-400 dark:text-gray-500 text-center py-6">
          No active orders.
        </div>
        <div v-for="order in activeOrders" :key="order.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
          <div class="flex justify-between mb-2">
            <span class="font-bold text-gray-800 dark:text-white">Order #{{ order.id }}</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ order.date }}</span>
          </div>
          <ul class="mb-2">
            <li v-for="item in order.items" :key="item.id" class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
              <span>{{ item.name }} x {{ item.quantity }}</span>
              <span>RM {{ (item.price * item.quantity).toFixed(2) }}</span>
            </li>
          </ul>
          <div class="flex justify-between font-bold text-gray-900 dark:text-white">
            <span>Total:</span>
            <span>RM {{ order.total.toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <!-- Past Orders Section -->
      <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4 tracking-wide">Past Orders</h3>
      <div class="space-y-4">
        <div v-if="pastOrders.length === 0" class="text-gray-400 dark:text-gray-500 text-center py-6">
          No past orders.
        </div>
        <div v-for="order in pastOrders" :key="order.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
          <div class="flex justify-between mb-2">
            <span class="font-bold text-gray-800 dark:text-white">Order #{{ order.id }}</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ order.date }}</span>
          </div>
          <ul class="mb-2">
            <li v-for="item in order.items" :key="item.id" class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
              <span>{{ item.name }} x {{ item.quantity }}</span>
              <span>RM {{ (item.price * item.quantity).toFixed(2) }}</span>
            </li>
          </ul>
          <div class="flex justify-between font-bold text-gray-900 dark:text-white">
            <span>Total:</span>
            <span>RM {{ order.total.toFixed(2) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const activeOrders = ref([])
const pastOrders = ref([])

const fetchOrders = async () => {
  try {
    const response = await axios.get('/api/orders') // your API endpoint
    activeOrders.value = response.data.filter(o => o.status === 'in_progress')
    pastOrders.value = response.data.filter(o => o.status === 'completed')
  } catch (err) {
    console.error('Failed to fetch orders', err)
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
</style>
