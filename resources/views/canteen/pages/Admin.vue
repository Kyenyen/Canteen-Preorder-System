<template>
  <!-- FIX: Changed 'h-full' to 'min-h-screen'. -->
  <!-- 'h-full' relies on the parent having a height. 'min-h-screen' ensures the background covers the full viewport height even if content is short. -->
  <div class="w-full min-h-screen flex flex-col fade-in p-6 overflow-y-auto bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Header -->
    <!-- Added shrink-0 to prevent header from squishing if screen is small -->
    <div class="flex justify-between items-center mb-8 shrink-0">
      <div class="flex items-center gap-4">
        <div class="bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm text-orange-600 dark:text-orange-400 transition-colors duration-300">
          <i class="fas fa-box-archive text-2xl"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white transition-colors duration-300">Order Management</h2>
          <p class="text-gray-500 dark:text-gray-400 text-sm transition-colors duration-300">Track and manage all customer orders</p>
        </div>
      </div>
      
      <!-- Status Filter -->
      <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Filter:</label>
        <select 
          v-model="selectedStatus" 
          class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-colors"
        >
          <option value="All">All Orders</option>
          <option value="Preparing">Preparing</option>
          <option value="Ready">Ready</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-300">
      <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 font-medium border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
          <tr>
            <th class="px-6 py-4">ID</th>
            <th class="px-6 py-4">User</th>
            <th class="px-6 py-4">Items</th>
            <th @click="toggleSort('pickup_time')" class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors select-none">
              <div class="flex items-center gap-2">
                Pickup Time
                <i v-if="sortBy === 'pickup_time'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'" class="text-xs"></i>
                <i v-else class="fas fa-sort text-xs opacity-30"></i>
              </div>
            </th>
            <th @click="toggleSort('total')" class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors select-none">
              <div class="flex items-center gap-2">
                Total
                <i v-if="sortBy === 'total'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'" class="text-xs"></i>
                <i v-else class="fas fa-sort text-xs opacity-30"></i>
              </div>
            </th>
            <th @click="toggleSort('status')" class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors select-none">
              <div class="flex items-center gap-2">
                Status
                <i v-if="sortBy === 'status'" :class="sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'" class="text-xs"></i>
                <i v-else class="fas fa-sort text-xs opacity-30"></i>
              </div>
            </th>
            <th class="px-6 py-4 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300 transition-colors duration-300">
          <tr v-for="order in filteredOrders" :key="order.order_id" class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors duration-150">
            <td class="px-6 py-4 font-mono text-xs">{{ order.order_id }}</td>
            <td class="px-6 py-4 font-medium">{{ order.user ? order.user.username : 'Unknown' }}</td>
            <td class="px-6 py-4">
              <ul class="list-disc list-inside text-xs">
                <li v-for="product in order.products" :key="product.product_id">
                  {{ product.name }} <span class="text-gray-400 dark:text-gray-500">x{{ product.pivot.quantity }}</span>
                </li>
              </ul>
            </td>
            <td class="px-6 py-4">{{ order.pickup_time }}</td>
            <td class="px-6 py-4">{{ formatCurrency(order.total) }}</td>
            <td class="px-6 py-4">
                <span :class="{
                    'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300': order.status === 'Preparing',
                    'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300': order.status === 'Ready' || order.status === 'Completed',
                    'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300': order.status === 'Cancelled'
                }" class="px-2 py-1 rounded-full text-xs font-semibold">
                    {{ order.status }}
                </span>
            </td>
            <td class="px-6 py-4 text-right">
              <button 
                v-if="order.status === 'Preparing'" 
                @click="updateStatus(order.order_id, 'Ready')" 
                class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition shadow-sm"
              >
                Mark Ready
              </button>
              <button 
                v-if="order.status === 'Ready'" 
                @click="updateStatus(order.order_id, 'Completed')" 
                class="px-3 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700 transition shadow-sm"
              >
                Mark Complete
              </button>
              <span 
                v-if="order.status === 'Completed'" 
                class="text-xs text-gray-400 dark:text-gray-500 italic"
              >
                Order Completed
              </span>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Empty State -->
      <div v-if="filteredOrders.length === 0" class="text-center py-10 text-gray-400 dark:text-gray-500">
        <i class="fas fa-clipboard-list text-4xl mb-3 opacity-50"></i>
        <p>No orders found for the selected status.</p>
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const orders = ref([])
const selectedStatus = ref('All')
const sortBy = ref('date')
const sortOrder = ref('desc')

const toggleSort = (column) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortOrder.value = 'asc'
  }
}

const filteredOrders = computed(() => {
  let filtered = orders.value
  
  // Filter by status
  if (selectedStatus.value !== 'All') {
    filtered = filtered.filter(order => order.status === selectedStatus.value)
  }
  
  // Sort
  const sorted = [...filtered].sort((a, b) => {
    let aVal, bVal
    
    switch(sortBy.value) {
      case 'date':
        aVal = new Date(a.date)
        bVal = new Date(b.date)
        break
      case 'pickup_time':
        aVal = a.pickup_time
        bVal = b.pickup_time
        break
      case 'total':
        aVal = parseFloat(a.total)
        bVal = parseFloat(b.total)
        break
      case 'status':
        aVal = a.status
        bVal = b.status
        break
      default:
        return 0
    }
    
    if (aVal < bVal) return sortOrder.value === 'asc' ? -1 : 1
    if (aVal > bVal) return sortOrder.value === 'asc' ? 1 : -1
    return 0
  })
  
  return sorted
})

const fetchOrders = async () => {
  try {
    const res = await axios.get('/api/admin/orders') 
    orders.value = res.data
  } catch (err) {
    console.error('Failed to fetch orders', err)
  }
}

const updateStatus = async (orderId, newStatus) => {
  try {
    await axios.put(`/api/admin/orders/${orderId}/status`, {
        status: newStatus
    })
    
    const order = orders.value.find(o => o.order_id === orderId)
    if(order) order.status = newStatus
    
  } catch (err) {
    console.error('Failed to update status', err)
    alert('Error updating status')
  }
}

const formatCurrency = (value) => {
    return 'RM ' + Number(value).toFixed(2)
}

onMounted(() => {
  fetchOrders()
  setInterval(fetchOrders, 30000)
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