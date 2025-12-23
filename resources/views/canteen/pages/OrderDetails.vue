<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-4 pb-20">
    <!-- Notification Component -->
    <Notification
      :show="notification.show"
      :type="notification.type"
      :title="notification.title"
      :message="notification.message"
      @close="notification.show = false"
    />

    <div class="max-w-3xl mx-auto">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-20">
        <i class="fas fa-spinner fa-spin text-5xl text-orange-600 mb-4"></i>
        <p class="text-gray-600 dark:text-gray-400">Loading order details...</p>
      </div>

      <!-- Order Details -->
      <div v-else-if="order" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Success Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 p-8 text-center text-white">
          <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-check text-4xl text-green-600"></i>
          </div>
          <h1 class="text-3xl font-bold mb-2">Order Confirmed!</h1>
          <p class="text-green-100">Your order has been placed successfully</p>
        </div>

        <!-- Order Info -->
        <div class="p-8">
          <!-- Order Number & Status -->
          <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-start mb-4">
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Order Number</p>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white">{{ order.order_id }}</h2>
              </div>
              <span class="px-4 py-2 rounded-full text-sm font-bold uppercase" :class="statusClass(order.status)">
                {{ order.status }}
              </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-4">
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Pickup Time</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ order.pickup_time }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Order Date</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ order.date }}</p>
              </div>
            </div>
          </div>

          <!-- Order Items -->
          <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Order Items</h3>
            <div class="space-y-3">
              <div v-for="item in order.products" :key="item.product_id" class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex-1">
                  <p class="font-semibold text-gray-900 dark:text-white">{{ item.name }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ item.pivot.quantity }}</p>
                </div>
                <p class="font-bold text-gray-900 dark:text-white">RM {{ (parseFloat(item.price) * item.pivot.quantity).toFixed(2) }}</p>
              </div>
            </div>
          </div>

          <!-- Order Note (if exists) -->
          <div v-if="order.note" class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Note</h3>
            <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded-r-lg">
              <p class="text-gray-700 dark:text-gray-300">{{ order.note }}</p>
            </div>
          </div>

          <!-- Payment Details -->
          <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Payment Details</h3>
            <div class="space-y-2">
              <div class="flex justify-between text-gray-700 dark:text-gray-300">
                <span>Subtotal</span>
                <span>RM {{ parseFloat(order.total).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-gray-700 dark:text-gray-300">
                <span>Payment Method</span>
                <span class="font-semibold">{{ formatPaymentMethod(order.payment) }}</span>
              </div>
              <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                <span class="text-xl font-bold text-gray-900 dark:text-white">Total</span>
                <span class="text-3xl font-bold text-orange-600">RM {{ parseFloat(order.total).toFixed(2) }}</span>
              </div>
            </div>
          </div>

          <!-- Receipt Email Notice -->
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
              <i class="fas fa-envelope text-blue-600 dark:text-blue-400 text-xl mt-1"></i>
              <div>
                <p class="font-semibold text-blue-900 dark:text-blue-300">Receipt Sent</p>
                <p class="text-sm text-blue-700 dark:text-blue-400">A copy of your receipt has been sent to your email address.</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="space-y-3">
            <button 
              @click="downloadReceipt" 
              :disabled="downloading"
              class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl transition-colors disabled:opacity-50 flex items-center justify-center gap-2 shadow-lg">
              <i :class="downloading ? 'fas fa-spinner fa-spin' : 'fas fa-download'"></i>
              {{ downloading ? 'Downloading...' : 'Download Receipt PDF' }}
            </button>
            
            <button 
              @click="goToOrders"
              class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-4 rounded-xl transition-colors shadow-lg">
              <i class="fas fa-list mr-2"></i>
              View All Orders
            </button>
            
            <button 
              @click="orderAgain"
              :disabled="reordering"
              class="w-full border-2 border-orange-600 text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 font-bold py-4 rounded-xl transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
              <i :class="reordering ? 'fas fa-spinner fa-spin' : 'fas fa-utensils'"></i>
              {{ reordering ? 'Adding to Cart...' : 'Order Again' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="text-center py-20">
        <i class="fas fa-exclamation-circle text-5xl text-red-500 mb-4"></i>
        <p class="text-xl text-gray-600 dark:text-gray-400">Order not found</p>
        <button @click="goToOrders" class="mt-6 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl transition-colors">
          View All Orders
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import { useCartStore } from '../../../js/stores/cart'
import Notification from '../components/Notification.vue'

const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()
const toggleCart = inject('toggleCart')
const order = ref(null)
const loading = ref(true)
const downloading = ref(false)
const reordering = ref(false)
const notification = ref({ show: false, title: '', message: '', type: 'success' })

const fetchOrder = async () => {
  try {
    const orderId = route.params.id
    const response = await axios.get(`/api/orders/${orderId}`)
    order.value = response.data
  } catch (error) {
    console.error('Failed to fetch order:', error)
    showNotification('Failed to load order details', 'error')
  } finally {
    loading.value = false
  }
}

const statusClass = (status) => {
  const classes = {
    'Pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'Preparing': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'Ready': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'Completed': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'Cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatPaymentMethod = (payment) => {
  if (!payment) return 'N/A'
  const method = payment.method || 'N/A'
  const methodMap = {
    'card': 'Credit/Debit Card',
    'FPX': 'FPX Online Banking',
    'GrabPay': 'GrabPay',
    'fpx': 'FPX Online Banking',
    'grabpay': 'GrabPay'
  }
  return methodMap[method] || method
}

const downloadReceipt = async () => {
  if (!order.value) return
  
  downloading.value = true
  try {
    const res = await axios.get(`/api/orders/${order.value.order_id}/receipt/download`, {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([res.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `Receipt_${order.value.order_id}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.parentNode.removeChild(link)
    window.URL.revokeObjectURL(url)
    
    showNotification('Receipt downloaded successfully', 'success')
  } catch (err) {
    console.error('Error downloading receipt:', err)
    showNotification('Failed to download receipt', 'error')
  } finally {
    downloading.value = false
  }
}

const goToOrders = () => {
  router.push('/history')
}

const orderAgain = async () => {
  if (!order.value || !order.value.products) return
  
  reordering.value = true
  try {
    // Add each product from the order to the cart
    for (const item of order.value.products) {
      await axios.post('/api/cart', {
        product_id: item.product_id,
        quantity: item.pivot.quantity
      })
    }
    
    // Refresh cart store
    await cartStore.fetchCart()
    
    showNotification(`${order.value.products.length} items added to cart!`, 'success')
    
    // Redirect to menu and open cart sidebar after showing notification
    setTimeout(() => {
      router.push('/menu')
      
      // Open cart after navigation
      setTimeout(() => {
        if (toggleCart) {
          toggleCart()
        }
      }, 300)
    }, 1000)
  } catch (err) {
    console.error('Error adding items to cart:', err)
    showNotification('Failed to add items to cart', 'error')
  } finally {
    reordering.value = false
  }
}

const showNotification = (message, type = 'success') => {
  notification.value = {
    show: true,
    title: type === 'success' ? 'Success' : 'Error',
    message: message,
    type: type
  }
}

onMounted(() => {
  fetchOrder()
})
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
