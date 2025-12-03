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
        <textarea v-model="note" rows="3" class="w-full p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 outline-none dark:text-white resize-none" placeholder="E.g., No onions, extra spicy, separate packaging..."></textarea>
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

      <!-- Pickup Time Section -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4">
        <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
          <i class="fas fa-clock text-orange-600"></i>
          Pickup Time
        </h2>
        <div class="relative">
          <select v-model="pickupTime" class="w-full p-3 pl-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-orange-500 outline-none appearance-none dark:text-white">
            <option v-for="time in pickupTimes" :key="time" :value="time">{{ time }}</option>
          </select>
          <i class="fas fa-clock absolute left-3 top-3.5 text-gray-400 dark:text-gray-500"></i>
          <i class="fas fa-chevron-down absolute right-3 top-3.5 text-gray-400 dark:text-gray-500 text-xs"></i>
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
        <button 
          @click="placeOrder" 
          :disabled="cartItems.length === 0 || !pickupTime"
          class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <i class="fas fa-shopping-cart"></i>
          <span>Place Order</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '../../../js/stores/cart'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const cartStore = useCartStore()

const note = ref('')
const pickupTime = ref('11:00 AM') // Set default pickup time
const paymentMethod = ref('ewallet')
const diningOption = ref('takeaway') // Set default dining option

const pickupTimes = ['11:00 AM', '11:30 AM', '12:00 PM', '12:30 PM', '1:00 PM'] // example

const diningOptions = [
  { value: 'takeaway', label: 'Takeaway', subtitle: 'Grab and go', icon: 'fas fa-bag-shopping' },
  { value: 'dine-in', label: 'Dine-In', subtitle: 'Eat at canteen', icon: 'fas fa-chair' }
]

const paymentMethods = [
  { value: 'ewallet', label: 'TARC eWallet', subtitle: 'Balance: RM 50.00', icon: 'fas fa-wallet' },
  { value: 'duitnow', label: 'DuitNow QR', subtitle: 'Scan to pay', icon: 'fas fa-qrcode' },
  { value: 'card', label: 'Credit / Debit Card', subtitle: '', icon: 'fas fa-credit-card' }
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

const placeOrder = async () => {
  if (!pickupTime.value) {
    alert('Please select a pickup time')
    return
  }

  try {
    const payload = {
      items: cartItems.value.map(item => ({
        product_id: item.id,
        quantity: item.qty,
        price: item.price
      })),
      note: note.value,
      pickup_time: pickupTime.value,
      payment_method: paymentMethod.value,
      dining_option: diningOption.value,
      total: total.value
    }
    
    const response = await axios.post('/api/orders', payload)
    
    // Clear cart after successful order
    await cartStore.clearCart()
    
    // Navigate to order confirmation or home
    router.push('/home')
  } catch (err) {
    console.error('Failed to place order', err)
    alert(err.response?.data?.message || 'Failed to place order. Please try again.')
  }
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