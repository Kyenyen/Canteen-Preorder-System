<template>
  <div class="h-full flex-col fade-in bg-gray-50 dark:bg-gray-900 overflow-y-auto transition-colors duration-300">
    <div class="max-w-4xl mx-auto w-full p-6 pb-32">

      <!-- Header -->
      <div class="flex items-center gap-4 mb-6">
        <button @click="goBack" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors">
          <i class="fas fa-arrow-left text-xl"></i>
        </button>
        <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Checkout</h1>
      </div>

      <!-- Order Items Card -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <i class="fas fa-receipt text-orange-600"></i>
            Order Items
          </h2>
          <button @click="goBack" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-semibold transition-colors">
            <i class="fas fa-plus mr-1"></i>Add More
          </button>
        </div>
        <div class="space-y-3">
          <div v-for="item in cartItems" :key="item.id" class="flex justify-between items-center">
            <div class="flex-1">
              <p class="font-semibold text-gray-800 dark:text-white">{{ item.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">RM {{ item.price.toFixed(2) }} × {{ item.qty }}</p>
            </div>
            <span class="font-bold text-gray-800 dark:text-white">RM {{ (item.price * item.qty).toFixed(2) }}</span>
          </div>
          <div v-if="cartItems.length === 0" class="text-center text-gray-400 dark:text-gray-500 py-4">
            Your cart is empty
          </div>
        </div>
      </div>

      <!-- Note Section -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
          <i class="fas fa-note-sticky text-orange-600"></i>
          Note (Optional)
        </h2>
        <textarea v-model="note" rows="3" maxlength="500" class="w-full p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 outline-none dark:text-white resize-none" placeholder="E.g., No onions, extra spicy, separate packaging..."></textarea>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ note.length }}/500 characters</p>
      </div>

      <!-- Dining Option Section -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
          <i class="fas fa-utensils text-orange-600"></i>
          Dining Option
        </h2>
        <div class="space-y-2">
          <label v-for="option in diningOptions" :key="option.value" class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-orange-500 dark:hover:border-orange-500 transition bg-gray-50 dark:bg-gray-700 group">
            <input type="radio" name="dining_option" v-model="diningOption" :value="option.value" class="w-4 h-4 text-orange-600 focus:ring-orange-500 border-gray-300">
            <div class="ml-3 flex-1">
              <span class="block text-sm font-bold text-gray-800 dark:text-white">{{ option.label }}</span>
              <span v-if="option.subtitle" class="block text-xs text-gray-500 dark:text-gray-400">{{ option.subtitle }}</span>
            </div>
            <i :class="option.icon" class="text-gray-400 group-hover:text-orange-500"></i>
          </label>
        </div>
      </div>

      <!-- Pickup Time Section (Takeaway) -->
      <div v-if="diningOption === 'takeaway'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
          <i class="fas fa-clock text-orange-600"></i>
          Pickup Time
        </h2>
        
        <!-- Sunday Closed Message -->
        <div v-if="pickupTimes.length === 0" class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl text-center">
          <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-2xl mb-2"></i>
          <p class="text-sm font-bold text-yellow-800 dark:text-yellow-300">Canteen Closed on Sundays</p>
          <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Please visit us Monday - Saturday</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            <i class="fas fa-clock mr-1"></i>Mon-Fri: 7:30 AM - 5:00 PM | Sat: 8:00 AM - 4:00 PM
          </p>
        </div>
        
        <!-- Pickup Time Selector -->
        <div v-else class="relative">
          <select v-model="pickupTime" class="w-full p-3 pl-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-orange-500 outline-none appearance-none dark:text-white">
            <option v-for="time in pickupTimes" :key="time" :value="time">{{ time }}</option>
          </select>
          <i class="fas fa-clock absolute left-3 top-3.5 text-gray-400 dark:text-gray-500"></i>
          <i class="fas fa-chevron-down absolute right-3 top-3.5 text-gray-400 dark:text-gray-500 text-xs"></i>
        </div>
      </div>

      <!-- Estimated Time Section (Dine-In) -->
      <div v-if="diningOption === 'dine-in'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
          <i class="fas fa-clock text-orange-600"></i>
          Estimated Preparation Time
        </h2>
        <div class="space-y-2">
          <label v-for="option in estimatedTimes" :key="option.value" class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-orange-500 dark:hover:border-orange-500 transition bg-gray-50 dark:bg-gray-700 group">
            <input type="radio" name="estimated_time" v-model="estimatedTime" :value="option.value" class="w-4 h-4 text-orange-600 focus:ring-orange-500 border-gray-300">
            <div class="ml-3 flex-1">
              <span class="block text-sm font-bold text-gray-800 dark:text-white">{{ option.label }}</span>
              <span v-if="option.subtitle" class="block text-xs text-gray-500 dark:text-gray-400">{{ option.subtitle }}</span>
            </div>
            <i :class="option.icon" class="text-gray-400 group-hover:text-orange-500"></i>
          </label>
        </div>
      </div>

      <!-- Payment Method Section -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
          <i class="fas fa-credit-card text-orange-600"></i>
          Payment Method
        </h2>
        <div class="space-y-2">
          <label v-for="method in paymentMethods" :key="method.value" class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-orange-500 dark:hover:border-orange-500 transition bg-gray-50 dark:bg-gray-700 group">
            <input type="radio" name="checkout_payment_method" v-model="paymentMethod" :value="method.value" class="w-4 h-4 text-orange-600 focus:ring-orange-500 border-gray-300">
            <div class="ml-3 flex-1">
              <span class="block text-sm font-bold text-gray-800 dark:text-white">{{ method.label }}</span>
              <span v-if="method.subtitle" class="block text-xs text-gray-500 dark:text-gray-400">{{ method.subtitle }}</span>
            </div>
            <i :class="method.icon" class="text-gray-400 group-hover:text-orange-500"></i>
          </label>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="bg-orange-50 dark:bg-orange-900/20 rounded-2xl border border-orange-200 dark:border-orange-800 p-6">
        <h2 class="text-sm font-bold text-orange-800 dark:text-orange-300 mb-3">Order Summary</h2>
        <div class="space-y-2">
          <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
            <span>Subtotal</span>
            <span>RM {{ subtotal.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-xl font-extrabold text-gray-900 dark:text-white border-t border-orange-200 dark:border-orange-700 pt-2">
            <span>Total</span>
            <span>RM {{ total.toFixed(2) }}</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Fixed Bottom Action -->
    <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-lg">
      <div class="max-w-4xl mx-auto">
        <!-- Error Message -->
        <div v-if="errorMessage" class="mb-3 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-lg text-sm flex items-center gap-2">
          <i class="fas fa-exclamation-circle"></i>
          <span>{{ errorMessage }}</span>
        </div>
        
        <button 
          @click="placeOrder" 
          :disabled="cartItems.length === 0 || (diningOption === 'takeaway' && (pickupTimes.length === 0 || !pickupTime)) || (diningOption === 'dine-in' && !estimatedTime)"
          class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <i class="fas fa-shopping-cart"></i>
          <span>Place Order</span>
        </button>
      </div>
    </div>

    <!-- Payment Modal -->
    <PaymentModal 
      :isOpen="showPaymentModal"
      :paymentMethod="paymentMethod"
      :amount="total"
      :orderData="orderData"
      @close="closePaymentModal"
      @confirm-payment="handlePaymentConfirm"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '../../../js/stores/cart'
import { useRouter } from 'vue-router'
import axios from 'axios'
import PaymentModal from '../components/Payment-modal.vue'

const router = useRouter()
const cartStore = useCartStore()

const note = ref('')
const pickupTime = ref('As Soon As Possible') // Set default pickup time
const estimatedTime = ref('asap') // Set default estimated time for dine-in
const paymentMethod = ref('fpx')
const diningOption = ref('takeaway') // Set default dining option
const showPaymentModal = ref(false)

// Computed property for order data to pass to payment modal
const orderData = computed(() => ({
  items: cartItems.value.map(item => ({
    product_id: item.id,
    quantity: item.qty,
  })),
  note: note.value,
  pickup_time: diningOption.value === 'takeaway' ? pickupTime.value : estimatedTime.value,
  payment_method: paymentMethod.value,
  dining_option: diningOption.value,
  total: total.value
}))

// Generate pickup times based on day of week
const pickupTimes = computed(() => {
  const today = new Date().getDay() // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
  const times = ['As Soon As Possible']
  
  // Sunday - Closed
  if (today === 0) {
    return []
  }
  
  let startHour, startMin, endHour
  
  // Saturday: 8:00 AM - 4:00 PM
  if (today === 6) {
    startHour = 8
    startMin = 0
    endHour = 16
  } 
  // Monday - Friday: 7:30 AM - 5:00 PM
  else {
    startHour = 7
    startMin = 30
    endHour = 17
  }
  
  // Generate 30-minute intervals
  for (let hour = startHour; hour <= endHour; hour++) {
    for (let min of [0, 30]) {
      // Skip if before start time or after end time
      if ((hour === startHour && min < startMin) || hour > endHour) continue
      if (hour === endHour && min > 0) continue
      
      const period = hour >= 12 ? 'PM' : 'AM'
      const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour)
      const displayMin = min.toString().padStart(2, '0')
      times.push(`${displayHour}:${displayMin} ${period}`)
    }
  }
  
  return times
})

const estimatedTimes = [
  { value: 'asap', label: 'As Soon As Possible', subtitle: 'We\'ll prepare right away', icon: 'fas fa-bolt' }
]

const diningOptions = [
  { value: 'takeaway', label: 'Takeaway', subtitle: 'Grab and go', icon: 'fas fa-bag-shopping' },
  { value: 'dine-in', label: 'Dine-In', subtitle: 'Eat at canteen', icon: 'fas fa-chair' }
]

const paymentMethods = [
  { value: 'fpx', label: 'FPX Online Banking', subtitle: 'Powered by Malaysia FPX', icon: 'fas fa-university' },
  { value: 'grabpay', label: 'GrabPay', subtitle: 'Pay with Grab Wallet', icon: 'fas fa-wallet' },
  { value: 'card', label: 'Credit / Debit Card', subtitle: 'Powered by Stripe', icon: 'fas fa-credit-card' }
]

// Use computed to get real-time cart items from store
const cartItems = computed(() => cartStore.items)

const subtotal = computed(() => cartStore.subtotal)
const total = computed(() => subtotal.value) // add tax/fees if needed

onMounted(() => {
  // Fetch latest cart data
  cartStore.fetchCart()
  
  // Redirect to menu if cart is empty
  if (cartStore.items.length === 0) {
    router.push('/menu')
  }
})

const goBack = () => {
  router.push('/menu') // go back to menu page
}

const errorMessage = ref('')

const placeOrder = async () => {
  errorMessage.value = ''
  
  // Check if Sunday and takeaway
  if (diningOption.value === 'takeaway' && pickupTimes.value.length === 0) {
    errorMessage.value = 'Sorry, the canteen is closed on Sundays'
    return
  }
  
  if (diningOption.value === 'takeaway' && !pickupTime.value) {
    errorMessage.value = 'Please select a pickup time'
    return
  }
  
  if (diningOption.value === 'dine-in' && !estimatedTime.value) {
    errorMessage.value = 'Please select an estimated time'
    return
  }

  // Debug: Log the order data to verify note is included
  console.log('Order Data:', orderData.value)

  // Just open the payment modal without creating the order yet
  // Order will be created after payment is successful
  showPaymentModal.value = true
}

const handlePaymentConfirm = async (paymentDetails) => {
  try {
    // If card, fpx, or grabpay payment (Stripe), order will be created in PaymentSuccess page
    if (paymentMethod.value === 'card' || paymentMethod.value === 'fpx' || paymentMethod.value === 'grabpay') {
      // Stripe payment - order creation happens after redirect
      showPaymentModal.value = false
      return
    }

    // For other payment methods, create order then payment
    const response = await axios.post('/api/orders', orderData.value)
    const orderId = response.data.order_id
    
    const paymentPayload = {
      order_id: orderId,
      method: paymentMethod.value
    }
    
    console.log('Processing payment:', paymentPayload)
    await axios.post('/api/payments', paymentPayload)
    
    showPaymentModal.value = false
    
    // Clear cart only after successful payment
    await cartStore.clearCart()
    
    // Show success message
    alert('Order placed and paid successfully!')
    
    // Navigate to home or order history
    router.push('/home')
    
  } catch (err) {
    console.error('Payment failed:', err)
    alert(err.response?.data?.message || 'Payment failed. Please try again.')
  }
}

const closePaymentModal = () => {
  // Just close the modal and return to checkout
  showPaymentModal.value = false
  // Keep the order and cart intact so user can try again or choose different payment method
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