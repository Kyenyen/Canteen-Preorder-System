<template>
  <!-- FIX: Changed 'h-full' to 'min-h-screen'. -->
  <!-- 'h-full' relies on the parent having a height. 'min-h-screen' ensures the background covers the full viewport height even if content is short. -->
  <div class="w-full min-h-screen flex flex-col fade-in p-6 overflow-y-auto bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    <!-- Confirmation Modal -->
    <ConfirmModal ref="confirmModal" :is-loading="isLoading" @confirm="handleConfirmCancel" />
    
    <!-- Notification -->
    <Notification
      :show="notification.show"
      :type="notification.type"
      :title="notification.title"
      :message="notification.message"
      @close="notification.show = false"
    />
    
    <!-- Header -->
    <!-- Added shrink-0 to prevent header from squishing if screen is small -->
    <div class="flex justify-between items-center mb-8 shrink-0">
      <div class="flex items-center gap-4">
        <div class="bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm text-orange-600 dark:text-orange-400 transition-colors duration-300">
          <i class="fas fa-box-archive text-2xl"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white transition-colors duration-300">Order Management</h2>
          <div class="flex items-center gap-2">
            <p class="text-gray-500 dark:text-gray-400 text-sm transition-colors duration-300">Track and manage all customer orders</p>
            <div v-if="isRefreshing" class="flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400">
              <i class="fas fa-sync-alt fa-spin"></i>
              <span>Syncing...</span>
            </div>
            <div v-else-if="lastRefresh" class="text-xs text-gray-400 dark:text-gray-500">
              <i class="fas fa-check-circle text-green-500"></i>
              Updated {{ lastRefresh }}
            </div>
          </div>
        </div>
      </div>
      
      <!-- Search and Filter -->
      <div class="flex items-center gap-3">
        <!-- Manual Refresh Button -->
        <button 
          @click="manualRefresh" 
          :disabled="isRefreshing"
          class="px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg text-sm transition-colors flex items-center gap-2 shadow-sm"
          title="Refresh orders"
        >
          <i :class="['fas fa-sync-alt', isRefreshing ? 'fa-spin' : '']"></i>
          <span class="hidden md:inline">Refresh</span>
        </button>
        
        <!-- Search -->
        <div class="relative">
          <input 
            v-model="searchQuery"
            type="text"
            placeholder="Search orders..."
            class="pl-10 pr-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-colors w-64"
          >
          <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
        </div>
        
        <!-- Status Filter -->
        <div class="flex items-center gap-2">
          <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Status:</label>
          <select 
            v-model="selectedStatus" 
            class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-colors"
          >
            <option value="All">All</option>
            <option value="Preparing">Preparing</option>
            <option value="Ready">Ready</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
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
            <th class="px-6 py-4">Note</th>
            <th class="px-6 py-4">Dining Option</th>
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
          <tr v-for="order in filteredOrders" :key="order.order_id" 
            :class="[
              'hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors duration-150',
              newOrderIds.has(order.order_id) ? 'bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-400 dark:ring-blue-600' : ''
            ]">
            <td class="px-6 py-4 font-mono text-xs">
              {{ order.order_id }}
              <span v-if="newOrderIds.has(order.order_id)" class="ml-2 px-2 py-0.5 bg-blue-600 text-white text-xs rounded-full animate-pulse">
                NEW
              </span>
            </td>
            <td class="px-6 py-4 font-medium">{{ order.user ? order.user.username : 'Unknown' }}</td>
            <td class="px-6 py-4">
              <ul class="list-disc list-inside text-xs">
                <li v-for="product in order.products" :key="product.product_id">
                  {{ product.name }} <span class="text-gray-400 dark:text-gray-500">x{{ product.pivot.quantity }}</span>
                </li>
              </ul>
            </td>
            <td class="px-6 py-4">
              <span v-if="order.note" class="text-xs italic text-orange-600 dark:text-orange-400">
                {{ order.note }}
              </span>
              <span v-else class="text-xs text-gray-400 dark:text-gray-500">-</span>
            </td>
            <td class="px-6 py-4">
              <span :class="{
                'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300': order.dining_option === 'Takeaway',
                'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300': order.dining_option === 'Dine In'
              }" class="px-2 py-1 rounded-full text-xs font-semibold">
                {{ order.dining_option || 'Takeaway' }}
              </span>
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
              <div class="flex gap-2 justify-end">
                <button 
                  v-if="order.status === 'Preparing'" 
                  @click="updateStatus(order.order_id, 'Ready')" 
                  class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition shadow-sm"
                >
                  Mark Ready
                </button>
                <button 
                  v-if="order.status === 'Preparing'" 
                  @click="cancelOrder(order.order_id)" 
                  class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition shadow-sm"
                >
                  Cancel
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
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Empty State -->
      <div v-if="filteredOrders.length === 0" class="text-center py-10 text-gray-400 dark:text-gray-500">
        <i class="fas fa-clipboard-list text-4xl mb-3 opacity-50"></i>
        <p v-if="searchQuery.trim() || selectedStatus !== 'All'">No orders found matching your filters.</p>
        <p v-else>No orders found.</p>
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed, reactive } from 'vue'
import axios from 'axios'
import ConfirmModal from '../components/Confirm-modal.vue'
import Notification from '../components/Notification.vue'

const orders = ref([])
const selectedStatus = ref('All')
const sortBy = ref('date')
const sortOrder = ref('desc')
const searchQuery = ref('')
const confirmModal = ref(null)
const notification = reactive({ show: false, type: 'success', title: '', message: '' })
const pendingOrderId = ref(null)
const isLoading = ref(false)
const isRefreshing = ref(false)
const lastRefresh = ref('')
const refreshInterval = ref(null)
const previousOrderCount = ref(0)
const newOrderIds = ref(new Set())

// Toggle Sort
const toggleSort = (column) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortOrder.value = 'asc'
  }
}

// Filtered Orders
const filteredOrders = computed(() => {
  let filtered = orders.value
  
  // Filter by status
  if (selectedStatus.value !== 'All') {
    filtered = filtered.filter(order => order.status === selectedStatus.value)
  }
  
  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(order => {
      const orderId = order.order_id.toLowerCase()
      const username = order.user?.username?.toLowerCase() || ''
      const items = order.products?.map(p => p.name.toLowerCase()).join(' ') || ''
      const pickupTime = order.pickup_time?.toLowerCase() || ''
      
      return orderId.includes(query) || 
             username.includes(query) || 
             items.includes(query) ||
             pickupTime.includes(query)
    })
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

// Fetch Orders
const fetchOrders = async (showRefreshIndicator = true) => {
  try {
    if (showRefreshIndicator) {
      isRefreshing.value = true
    }
    
    const res = await axios.get('/api/admin/orders')
    const newOrders = res.data
    
    // Detect new orders
    if (orders.value.length > 0) {
      const currentOrderIds = new Set(orders.value.map(o => o.order_id))
      const incomingOrderIds = newOrders.map(o => o.order_id)
      
      // Find newly added orders
      const newlyAdded = incomingOrderIds.filter(id => !currentOrderIds.has(id))
      if (newlyAdded.length > 0) {
        // Mark new orders for highlighting
        newOrderIds.value = new Set(newlyAdded)
        
        // Show notification for new orders
        notification.type = 'info'
        notification.title = 'New Order Received'
        notification.message = `${newlyAdded.length} new order${newlyAdded.length > 1 ? 's' : ''} received`
        notification.show = true
        
        // Play notification sound (optional)
        try {
          const audio = new Audio('/notification.mp3')
          audio.volume = 0.5
          audio.play().catch(() => {})
        } catch (e) {
          // Ignore audio errors
        }
        
        // Remove highlight after 5 seconds
        setTimeout(() => {
          newOrderIds.value.clear()
        }, 5000)
      }
    }
    
    orders.value = newOrders
    previousOrderCount.value = newOrders.length
    
    // Update last refresh time
    const now = new Date()
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
    lastRefresh.value = timeStr
    
  } catch (err) {
    console.error('Failed to fetch orders', err)
  } finally {
    if (showRefreshIndicator) {
      isRefreshing.value = false
    }
  }
}

// Update Status
const updateStatus = async (orderId, newStatus) => {
  try {
    await axios.put(`/api/admin/orders/${orderId}/status`, {
        status: newStatus
    })
    
    const order = orders.value.find(o => o.order_id === orderId)
    if(order) order.status = newStatus
    
    notification.type = 'success'
    notification.title = 'Status Updated'
    notification.message = `Order status updated to ${newStatus}`
    notification.show = true
  } catch (err) {
    console.error('Failed to update status', err)
    notification.type = 'error'
    notification.title = 'Update Failed'
    notification.message = 'Error updating status'
    notification.show = true
  }
}

// Cancel Order
const cancelOrder = (orderId) => {
  pendingOrderId.value = orderId
  confirmModal.value?.openModal({
    customTitle: 'Cancel Order',
    customMessage: 'Are you sure you want to cancel this order? The customer will be notified via email.',
    customIcon: 'fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400'
  })
}

// Handle Confirm Cancel
const handleConfirmCancel = async () => {
  if (!pendingOrderId.value) return
  
  const orderId = pendingOrderId.value
  isLoading.value = true
  
  try {
    await axios.post(`/api/admin/orders/${orderId}/cancel`, {})
    
    const order = orders.value.find(o => o.order_id === orderId)
    if(order) order.status = 'Cancelled'
    
    notification.type = 'success'
    notification.title = 'Order Cancelled'
    notification.message = 'Order cancelled successfully. Customer notified via email.'
    notification.show = true
    pendingOrderId.value = null
  } catch (err) {
    console.error('Failed to cancel order', err)
    notification.type = 'error'
    notification.title = 'Cancellation Failed'
    notification.message = err.response?.data?.message || 'Error cancelling order'
    notification.show = true
    pendingOrderId.value = null
  } finally {
    isLoading.value = false
    confirmModal.value?.closeModal()
  }
}

// Format Currency
const formatCurrency = (value) => {
    return 'RM ' + Number(value).toFixed(2)
}

// Manual Refresh
const manualRefresh = async () => {
  await fetchOrders(true)
}

// Start Auto Refresh
const startAutoRefresh = () => {
  // Clear existing interval if any
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
  }
  
  // Auto-refresh every 5 seconds (more frequent for real-time feel)
  refreshInterval.value = setInterval(() => {
    fetchOrders(false) // Silent refresh without indicator
  }, 5000)
}

// Stop Auto Refresh
const stopAutoRefresh = () => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
    refreshInterval.value = null
  }
}

onMounted(() => {
  fetchOrders()
  startAutoRefresh()
})

onBeforeUnmount(() => {
  stopAutoRefresh()
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