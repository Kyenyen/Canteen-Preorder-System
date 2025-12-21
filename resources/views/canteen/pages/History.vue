<template>
  <!-- VIEW: HISTORY (McDonald's Style) -->
  <div id="view-history" class="h-full flex-col fade-in bg-gray-100 dark:bg-gray-900 overflow-y-auto transition-colors duration-300">
    <!-- Notification Component -->
    <Notification
      :show="notification.show"
      :type="notification.type"
      :title="notification.title"
      :message="notification.message"
      @close="notification.show = false"
    />

    <!-- Reorder Notification Component -->
    <Notification 
      :show="showReorderNotification" 
      type="success" 
      title="Items Added" 
      :message="reorderMessage"
      @close="showReorderNotification = false"
    />

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
            <div class="flex gap-2">
              <button 
                v-if="order.status === 'Completed'"
                @click="viewReceipt(order.order_id)"
                class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                <i class="fas fa-eye text-xs"></i>
                View
              </button>
              <button 
                v-if="order.status === 'Completed'"
                @click="sendEmailReceipt(order.order_id)"
                :disabled="sendingEmail === order.order_id"
                class="px-3 py-1.5 border-2 border-orange-500 text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 disabled:opacity-50 text-sm font-semibold rounded-lg transition-colors disabled:cursor-not-allowed flex items-center gap-1.5">
                <i :class="sendingEmail === order.order_id ? 'fas fa-spinner fa-spin text-xs' : 'fas fa-envelope text-xs'"></i>
                Email
              </button>
              <button 
                v-if="order.status === 'Completed'"
                @click="downloadReceipt(order.order_id)"
                :disabled="downloadingOrder === order.order_id"
                class="px-3 py-1.5 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 text-sm font-semibold rounded-lg transition-colors disabled:cursor-not-allowed flex items-center gap-1.5">
                <i :class="downloadingOrder === order.order_id ? 'fas fa-spinner fa-spin text-xs' : 'fas fa-download text-xs'"></i>
                PDF
              </button>
              <button 
                @click="reorder(order.order_id)"
                class="px-3 py-1.5 border-2 border-orange-500 text-orange-500 text-sm font-semibold rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors flex items-center gap-1.5">
                <i class="fas fa-redo text-xs"></i> Order Again
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Receipt View Modal -->
    <div v-if="showReceiptModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 fade-in backdrop-blur-sm" @click.self="showReceiptModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 flex justify-between items-center">
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Receipt Details</h3>
          <button @click="showReceiptModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Receipt Content -->
        <div v-if="receiptData" class="p-6">
          <!-- Order Info -->
          <div class="text-center mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">UniCanteen</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">Order Receipt</p>
          </div>

          <!-- Order Details -->
          <div class="space-y-3 mb-6">
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Order ID:</span>
              <span class="font-semibold text-gray-900 dark:text-white">{{ receiptData.order_id }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Date:</span>
              <span class="font-semibold text-gray-900 dark:text-white">{{ receiptData.date }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Pickup Time:</span>
              <span class="font-semibold text-gray-900 dark:text-white">{{ receiptData.pickup_time }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Status:</span>
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">{{ receiptData.status }}</span>
            </div>
          </div>

          <!-- Items -->
          <div class="mb-6">
            <h5 class="font-bold text-gray-900 dark:text-white mb-3">Items Ordered</h5>
            <div class="space-y-2">
              <div v-for="item in receiptData.products" :key="item.product_id" class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ item.name }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.pivot.quantity }} x RM {{ parseFloat(item.price).toFixed(2) }}</p>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white">RM {{ parseFloat(item.pivot.subtotal).toFixed(2) }}</p>
              </div>
            </div>
          </div>

          <!-- Total -->
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center">
              <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
              <span class="text-2xl font-bold text-orange-600 dark:text-orange-400">RM {{ parseFloat(receiptData.total).toFixed(2) }}</span>
            </div>
          </div>

          <!-- Payment Info -->
          <div v-if="receiptData.payment" class="text-sm text-gray-600 dark:text-gray-400 text-center space-y-1">
            <p>Payment Method: <span class="font-semibold">{{ formatPaymentMethod(receiptData.payment.method) }}</span></p>
            <p>Payment ID: <span class="font-mono text-xs">{{ receiptData.payment.payment_id }}</span></p>
          </div>
        </div>

        <!-- Loading State -->
        <div v-else class="p-6 text-center">
          <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
          <p class="text-gray-500 dark:text-gray-400">Loading receipt...</p>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-700 p-4 flex gap-3">
          <button @click="showReceiptModal = false" class="flex-1 px-4 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold rounded-xl hover:bg-gray-300 dark:hover:bg-gray-500 transition">
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Cancel Order Confirmation Modal -->
    <div v-if="showCancelModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 fade-in backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="text-center mb-6">
          <div class="w-16 h-16 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-exclamation-triangle text-3xl text-red-600 dark:text-red-400"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Cancel Order?</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-3">Are you sure you want to cancel order <span class="font-semibold text-gray-900 dark:text-white">#{{ orderToCancel }}</span>?</p>
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-2">
            <div class="flex items-start gap-2">
              <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
              <p class="text-sm text-blue-800 dark:text-blue-300 text-left">Your refund will be processed within <span class="font-semibold">3 working days</span> to your original payment method.</p>
            </div>
          </div>
          <p class="text-sm text-red-600 dark:text-red-400 mt-2">This action cannot be undone.</p>
        </div>
        <div class="flex gap-3">
          <button
            @click="showCancelModal = false"
            class="flex-1 px-4 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition"
          >
            Keep Order
          </button>
          <button
            @click="confirmCancelOrder"
            :disabled="cancelling !== null"
            class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-semibold rounded-xl transition disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <i v-if="cancelling !== null" class="fas fa-spinner fa-spin"></i>
            {{ cancelling !== null ? 'Cancelling...' : 'Yes, Cancel' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed } from 'vue'
import axios from 'axios'
import { useCartStore } from '../../../js/stores/cart'
import { useRouter } from 'vue-router'
import Notification from '../components/Notification.vue'

const orders = ref([])
const cancelling = ref(null)
const downloadingOrder = ref(null)
const sendingEmail = ref(null)
const cartStore = useCartStore()
const router = useRouter()
const notification = reactive({ show: false, title: '', message: '', type: 'success' })

// Cancel confirmation modal
const showCancelModal = ref(false)
const orderToCancel = ref(null)

// Receipt modal
const showReceiptModal = ref(false)
const receiptData = ref(null)

// Reorder notification
const showReorderNotification = ref(false)
const reorderMessage = ref('')

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

const cancelOrder = (orderId) => {
  orderToCancel.value = orderId
  showCancelModal.value = true
}

const confirmCancelOrder = async () => {
  const orderId = orderToCancel.value
  cancelling.value = orderId
  
  try {
    await axios.put(`/api/orders/${orderId}/cancel`)
    
    // Send cancellation email
    try {
      await axios.post(`/api/orders/${orderId}/send-cancellation-email`)
      console.log('Cancellation email sent')
    } catch (emailErr) {
      console.error('Failed to send cancellation email:', emailErr)
    }
    
    await fetchOrders()
    showCancelModal.value = false
    orderToCancel.value = null
    showNotification('Order cancelled successfully. A confirmation email has been sent.', 'success')
  } catch (err) {
    console.error('Failed to cancel order', err)
    showNotification(err.response?.data?.message || 'Failed to cancel order', 'error')
  } finally {
    cancelling.value = null
  }
}

const showNotification = (message, type = 'success') => {
  notification.title = type === 'success' ? 'Success' : 'Error'
  notification.message = message
  notification.type = type
  notification.show = true
}

const reorder = async (orderId) => {
  try {
    const order = orders.value.find(o => o.order_id === orderId)
    if (!order || !order.products) return

    // Add all items from the order to cart
    for (const item of order.products) {
      await cartStore.addItem(item, item.pivot.quantity)
    }
    
    reorderMessage.value = `${order.products.length} item(s) added to cart!`
    showReorderNotification.value = true
    
    // Navigate to menu page after a short delay
    setTimeout(() => {
      router.push('/menu')
    }, 800)
  } catch (err) {
    console.error('Failed to reorder', err)
    showNotification('Failed to add items to cart. Please try again.', 'error')
  }
}

const downloadReceipt = async (orderId) => {
  downloadingOrder.value = orderId
  try {
    const res = await axios.get(`/api/orders/${orderId}/receipt/download`, {
      responseType: 'blob'
    })
    
    // Create blob link to download
    const url = window.URL.createObjectURL(new Blob([res.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `Receipt_${orderId}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.parentNode.removeChild(link)
    window.URL.revokeObjectURL(url)
    
    showNotification('Receipt downloaded successfully', 'success')
  } catch (err) {
    console.error('Error downloading receipt:', err)
    showNotification('Failed to download receipt', 'error')
  } finally {
    downloadingOrder.value = null
  }
}

const viewReceipt = async (orderId) => {
  showReceiptModal.value = true
  receiptData.value = null
  
  try {
    const order = orders.value.find(o => o.order_id === orderId)
    if (order) {
      receiptData.value = order
    }
  } catch (err) {
    console.error('Error loading receipt:', err)
    showNotification('Failed to load receipt', 'error')
    showReceiptModal.value = false
  }
}

const sendEmailReceipt = async (orderId) => {
  sendingEmail.value = orderId
  
  try {
    await axios.post(`/api/orders/${orderId}/request-receipt`)
    showNotification('Receipt email sent successfully', 'success')
  } catch (err) {
    console.error('Error sending receipt email:', err)
    showNotification('Failed to send receipt email', 'error')
  } finally {
    sendingEmail.value = null
  }
}
const formatPaymentMethod = (method) => {
  if (!method) return 'N/A'
  
  const methodMap = {
    'card': 'Credit/Debit Card',
    'fpx': 'FPX',
    'grabpay': 'GrabPay'
  }
  
  return methodMap[method.toLowerCase()] || method
}
// Polling interval for real-time updates
let pollingInterval = null

onMounted(() => {
  fetchOrders()
  
  // Poll for order updates every 5 seconds
  pollingInterval = setInterval(() => {
    fetchOrders()
  }, 5000)
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

/* Pulse animation is built into Tailwind CSS via animate-pulse class */
</style>
