<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 max-w-md w-full text-center">
      <!-- Loading State -->
      <div v-if="isProcessing" class="space-y-4">
        <div class="w-16 h-16 mx-auto">
          <i class="fas fa-spinner fa-spin text-5xl text-blue-600"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Processing Payment...</h2>
        <p class="text-gray-600 dark:text-gray-400">Please wait while we confirm your payment</p>
      </div>

      <!-- Success State -->
      <div v-else-if="status === 'success'" class="space-y-4">
        <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center">
          <i class="fas fa-check text-3xl text-green-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Payment Successful!</h2>
        <p class="text-gray-600 dark:text-gray-400">Your order has been confirmed and paid.</p>
        <button @click="goToHome" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition">
          <i class="fas fa-home mr-2"></i>
          Go to Home
        </button>
      </div>

      <!-- Error State -->
      <div v-else-if="status === 'error'" class="space-y-4">
        <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center">
          <i class="fas fa-times text-3xl text-red-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Payment Failed</h2>
        <p class="text-gray-600 dark:text-gray-400">{{ errorMessage || 'Something went wrong with your payment.' }}</p>
        <button @click="goToHome" class="w-full mt-6 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded-xl transition">
          <i class="fas fa-arrow-left mr-2"></i>
          Back to Home
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../../../js/stores/cart'
import axios from 'axios'

const router = useRouter()
const cartStore = useCartStore()
const isProcessing = ref(true)
const status = ref(null)
const errorMessage = ref('')
const paymentId = ref('')

onMounted(async () => {
  const urlParams = new URLSearchParams(window.location.search)
  const paymentIntentClientSecret = urlParams.get('payment_intent_client_secret')
  const redirectStatus = urlParams.get('redirect_status')
  const paymentIntentId = urlParams.get('payment_intent')
  const paymentMethod = urlParams.get('payment_method')
  const orderDataEncoded = urlParams.get('order_data')

  console.log('Payment return URL params:', {
    paymentIntentClientSecret,
    redirectStatus,
    paymentIntentId,
    paymentMethod,
    orderDataEncoded,
    fullURL: window.location.href
  })

  // Decode order data
  let orderData = null
  try {
    orderData = orderDataEncoded ? JSON.parse(decodeURIComponent(orderDataEncoded)) : null
  } catch (e) {
    console.error('Failed to parse order data:', e)
  }

  // Only require payment_intent and order_data
  if (!paymentIntentId || !orderData) {
    status.value = 'error'
    errorMessage.value = 'Invalid payment session - missing payment intent or order data'
    isProcessing.value = false
    console.error('Missing required params:', { paymentIntentId, orderData })
    return
  }

  if (redirectStatus === 'succeeded') {
    try {
      // Determine the correct payment method format for database
      let dbPaymentMethod = paymentMethod || 'card'
      if (dbPaymentMethod === 'fpx') {
        dbPaymentMethod = 'FPX' // Store as uppercase FPX
      } else if (dbPaymentMethod === 'grabpay') {
        dbPaymentMethod = 'GrabPay' // Store as GrabPay
      }
      // card stays as 'card'
      
      // Confirm payment and create order on backend
      const response = await axios.post('/api/payments/stripe/confirm', {
        payment_intent_id: paymentIntentId,
        payment_method: dbPaymentMethod,
        order_data: orderData
      })

      console.log('Order created and payment confirmed:', response.data)
      
      // Clear the cart after successful order creation
      await cartStore.clearCart()
      
      status.value = 'success'
      paymentId.value = response.data.payment_id || 'N/A'
      isProcessing.value = false

      // Auto redirect to home after 3 seconds
      setTimeout(() => {
        goToHome()
      }, 3000)

    } catch (error) {
      console.error('Failed to confirm payment:', error)
      status.value = 'error'
      errorMessage.value = error.response?.data?.message || error.response?.data?.error || 'Failed to confirm payment'
      isProcessing.value = false
    }
  } else {
    status.value = 'error'
    errorMessage.value = redirectStatus === 'failed' 
      ? 'Payment was cancelled or failed at the bank' 
      : 'Payment was not completed'
    isProcessing.value = false
  }
})

const goToHome = () => {
  router.push('/home')
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
