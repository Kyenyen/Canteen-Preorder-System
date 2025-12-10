<template>
  <!-- Modal Backdrop -->
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 fade-in backdrop-blur-sm">
    
    <!-- Modal Content -->
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all scale-100 flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="bg-gray-50 dark:bg-gray-750 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                <i :class="[methodIcon, 'mr-2', paymentMethod === 'fpx' ? 'text-blue-600' : paymentMethod === 'grabpay' ? 'text-green-600' : 'text-orange-500']"></i>
                {{ methodTitle }} Payment
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Content Area -->
        <div class="p-6 overflow-y-auto">
            
            <!-- 1. QR Code View -->
            <div v-if="paymentMethod === 'duitnow' || paymentMethod === 'qr'" class="flex flex-col items-center text-center">
                <div class="bg-white p-4 rounded-xl shadow-sm mb-4 border border-gray-200">
                    <div class="w-48 h-48 bg-gray-900 flex items-center justify-center text-white rounded-lg">
                        <!-- You can replace this icon with a real <img> tag later -->
                        <i class="fas fa-qrcode text-6xl"></i>
                    </div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Scan this QR code with your preferred banking app to complete the payment.</p>
                
                <button @click="processPayment" :disabled="isLoading" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                    <i v-else class="fas fa-check-circle"></i>
                    <span>{{ isLoading ? 'Processing...' : 'I Have Paid' }}</span>
                </button>
            </div>

            <!-- 2. Card Form View (Stripe) -->
            <div v-if="paymentMethod === 'card'" class="space-y-4">
                <div v-if="errorMessage" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 mb-4">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
                </div>
                
                <div v-if="!stripeInitialized" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Initializing secure payment...</p>
                </div>

                <!-- Stripe Payment Element will be mounted here -->
                <div ref="paymentElementContainer" class="mb-4" :class="{ 'hidden': !stripeInitialized }"></div>

                <button v-if="stripeInitialized" @click="processStripePayment" :disabled="isLoading" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50">
                    <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                    <i v-else class="fas fa-lock"></i>
                    <span>{{ isLoading ? 'Processing...' : `Pay RM ${stripeAmount.toFixed(2)}` }}</span>
                </button>

                <div v-if="stripeInitialized" class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secured by Stripe</span>
                </div>
            </div>

            <!-- 3. FPX Form View (Stripe) -->
            <div v-if="paymentMethod === 'fpx'" class="space-y-4">
                <!-- FPX Info Banner -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-1">Pay with Malaysia FPX</p>
                            <p class="text-xs text-blue-700 dark:text-blue-400">You will be redirected to your bank's login page to authorize the payment securely.</p>
                        </div>
                    </div>
                </div>

                <div v-if="errorMessage" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 mb-4">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
                </div>
                
                <div v-if="!stripeInitialized" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-400 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Connecting to FPX...</p>
                </div>

                <!-- Stripe Payment Element will be mounted here -->
                <div ref="paymentElementContainer" class="mb-4" :class="{ 'hidden': !stripeInitialized }"></div>

                <button v-if="stripeInitialized" @click="processStripePayment" :disabled="isLoading" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50">
                    <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                    <i v-else class="fas fa-university"></i>
                    <span>{{ isLoading ? 'Redirecting to bank...' : `Pay RM ${stripeAmount.toFixed(2)} with FPX` }}</span>
                </button>

                <div v-if="stripeInitialized" class="mt-4 space-y-2">
                    <div class="flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secured by Stripe & Malaysia FPX</span>
                    </div>
                    <div class="flex items-center justify-center gap-2 text-xs text-blue-600 dark:text-blue-400">
                        <i class="fas fa-lock"></i>
                        <span>Bank-level security encryption</span>
                    </div>
                </div>
            </div>

            <!-- 4. GrabPay Form View (Stripe) -->
            <div v-if="paymentMethod === 'grabpay'" class="space-y-4">
                <!-- GrabPay Info Banner -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-wallet text-green-600 dark:text-green-400 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-green-900 dark:text-green-300 mb-1">Pay with GrabPay</p>
                            <p class="text-xs text-green-700 dark:text-green-400">You will be redirected to the Grab app to complete your payment.</p>
                        </div>
                    </div>
                </div>

                <div v-if="errorMessage" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 mb-4">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
                </div>
                
                <div v-if="!stripeInitialized" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-green-400 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Connecting to GrabPay...</p>
                </div>

                <!-- Stripe Payment Element will be mounted here -->
                <div ref="paymentElementContainer" class="mb-4" :class="{ 'hidden': !stripeInitialized }"></div>

                <button v-if="stripeInitialized" @click="processStripePayment" :disabled="isLoading" class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50">
                    <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                    <i v-else class="fas fa-wallet"></i>
                    <span>{{ isLoading ? 'Redirecting to Grab...' : `Pay RM ${stripeAmount.toFixed(2)} with GrabPay` }}</span>
                </button>

                <div v-if="stripeInitialized" class="mt-4 space-y-2">
                    <div class="flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secured by Stripe & GrabPay</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { loadStripe } from '@stripe/stripe-js'

const props = defineProps({
    isOpen: Boolean,
    paymentMethod: String, // 'card', 'fpx', 'duitnow'
    amount: Number,
    orderData: Object // Order data to be created after payment
})

const emit = defineEmits(['close', 'confirm-payment'])

const isLoading = ref(false)
const errorMessage = ref('')

const stripe = ref(null)
const elements = ref(null)
const stripeInitialized = ref(false)
const paymentIntentId = ref(null)
const paymentElementContainer = ref(null)
const stripeAmount = ref(0) // Store the actual amount from PaymentIntent

// Form States
const cardForm = reactive({
    number: '',
    expiry: '',
    cvv: '',
    pin: ''
})

const walletForm = reactive({
    phone: '',
    pin: ''
})

// Computed UI Helpers
const methodTitle = computed(() => {
    switch(props.paymentMethod) {
        case 'card': return 'Card';
        case 'fpx': return 'FPX Online Banking';
        case 'grabpay': return 'GrabPay';
        case 'duitnow': return 'DuitNow QR';
        default: return 'Payment';
    }
})

const methodIcon = computed(() => {
    switch(props.paymentMethod) {
        case 'card': return 'fa-credit-card';
        case 'fpx': return 'fa-university';
        case 'grabpay': return 'fa-wallet';
        case 'duitnow': return 'fa-qrcode';
        default: return 'fa-money-bill';
    }
})

// Watch for modal open and payment method change to initialize Stripe
watch(() => [props.isOpen, props.paymentMethod], async ([isOpen, method]) => {
    if (isOpen && (method === 'card' || method === 'fpx' || method === 'grabpay') && !stripeInitialized.value) {
        // Wait for DOM to be updated
        await nextTick()
        // Delay to ensure the modal and stripe container are fully rendered
        setTimeout(() => {
            initializeStripe()
        }, 300)
    }
})

// Initialize Stripe
const initializeStripe = async () => {
    try {
        errorMessage.value = ''
        stripeInitialized.value = false

        // Get Stripe publishable key from env
        const stripeKey = import.meta.env.VITE_STRIPE_KEY
        
        if (!stripeKey) {
            throw new Error('Stripe key not configured')
        }

        // Create Payment Intent
        const response = await axios.post('/api/payments/stripe/intent', {
            amount: props.amount,
            payment_method_type: props.paymentMethod, // Send the actual payment method (card, fpx, grabpay)
            order_data: props.orderData // Send order data for metadata
        })

        const { clientSecret, paymentIntentId: intentId, amount } = response.data
        paymentIntentId.value = intentId
        
        // Store the amount from PaymentIntent (convert from cents to dollars)
        if (amount) {
            stripeAmount.value = amount / 100
        }

        // Initialize Stripe.js
        stripe.value = await loadStripe(stripeKey)
        
        // Create Elements instance
        const appearance = {
            theme: 'stripe',
            variables: {
                colorPrimary: props.paymentMethod === 'fpx' ? '#0066cc' : '#2563eb',
                colorBackground: '#ffffff',
                colorText: '#1f2937',
                colorDanger: '#ef4444',
                fontFamily: 'system-ui, sans-serif',
                borderRadius: '8px',
            }
        }

        elements.value = stripe.value.elements({
            clientSecret,
            appearance
        })

        // Create and mount the Payment Element
        const paymentElement = elements.value.create('payment', {
            layout: props.paymentMethod === 'fpx' ? {
                type: 'accordion',
                defaultCollapsed: false,
                radios: true,
                spacedAccordionItems: false
            } : 'tabs'
        })
        
        // Verify the element exists before mounting
        if (!paymentElementContainer.value) {
            throw new Error('Payment element container not found in DOM')
        }
        
        paymentElement.mount(paymentElementContainer.value)

        stripeInitialized.value = true
        stripeInitialized.value = true

    } catch (error) {
        console.error('Stripe initialization error:', error)
        errorMessage.value = error.response?.data?.message || error.message || 'Failed to initialize payment'
    }
}

// Process Stripe Payment
const processStripePayment = async () => {
    if (!stripe.value || !elements.value) {
        errorMessage.value = 'Payment system not initialized'
        return
    }

    isLoading.value = true
    errorMessage.value = ''

    try {
        console.log('Starting Stripe payment confirmation for:', props.paymentMethod)
        
        // Both card and FPX use 'always' redirect to payment success page
        const redirectBehavior = 'always'
        
        // Encode order data to pass in URL
        const orderDataEncoded = encodeURIComponent(JSON.stringify(props.orderData))
        
        // Confirm the payment with Stripe
        const { error, paymentIntent } = await stripe.value.confirmPayment({
            elements: elements.value,
            redirect: redirectBehavior,
            confirmParams: {
                return_url: `${window.location.origin}/payment-success?payment_method=${props.paymentMethod}&order_data=${orderDataEncoded}`
            }
        })

        if (error) {
            console.error('Stripe confirmPayment error:', error)
            throw new Error(error.message)
        }

        // This code will not be reached due to 'always' redirect
        if (paymentIntent && paymentIntent.status === 'succeeded') {
            console.log('Payment succeeded:', paymentIntent.id)
        }

    } catch (error) {
        console.error('Stripe payment error:', error)
        errorMessage.value = error.message || 'Payment failed. Please try again.'
        isLoading.value = false
    }
}

// Confirm Stripe payment on backend
const confirmStripePaymentOnBackend = async () => {
    try {
        console.log('Confirming payment on backend:', {
            order_id: props.orderId,
            payment_intent_id: paymentIntentId.value,
            payment_method: props.paymentMethod
        })
        
        const response = await axios.post('/api/payments/stripe/confirm', {
            order_id: props.orderId,
            payment_intent_id: paymentIntentId.value,
            payment_method: props.paymentMethod // Send the payment method (card or fpx)
        })

        console.log('Backend confirmation response:', response.data)
        isLoading.value = false
        
        // Emit success to parent
        emit('confirm-payment', {
            method: props.paymentMethod, // Use actual payment method (card or ewallet)
            paymentId: response.data.payment_id,
            success: true
        })

    } catch (error) {
        console.error('Payment confirmation error:', error)
        errorMessage.value = error.response?.data?.message || 'Payment confirmation failed'
        isLoading.value = false
    }
}

// --- Input Formatters ---

// Card: 0000 0000 0000 0000
const formatCardNumber = (e) => {
    let value = e.target.value.replace(/\D/g, '').substring(0, 16)
    value = value.match(/.{1,4}/g)?.join(' ') || value
    cardForm.number = value
}

// Expiry: MM/YY
const formatExpiry = (e) => {
    let value = e.target.value.replace(/\D/g, '')
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4)
    }
    cardForm.expiry = value
}

// Generic Number Only
const onlyNumbers = (field, max) => {
    let val = cardForm[field].replace(/\D/g, '')
    cardForm[field] = val.slice(0, max)
}

const onlyNumbersWallet = (field, max) => {
    let val = walletForm[field].replace(/\D/g, '')
    walletForm[field] = val.slice(0, max)
}

const formatPhone = (e) => {
    // Just simple number filtering for phone
    walletForm.phone = e.target.value.replace(/\D/g, '')
}

// Check if returning from FPX redirect on component mount
onMounted(async () => {
    const urlParams = new URLSearchParams(window.location.search)
    const paymentIntentClientSecret = urlParams.get('payment_intent_client_secret')
    const redirectStatus = urlParams.get('redirect_status')
    
    if (paymentIntentClientSecret && redirectStatus) {
        console.log('Returned from payment redirect:', { redirectStatus })
        
        if (redirectStatus === 'succeeded') {
            // Payment succeeded, need to confirm on backend
            const paymentIntentId = urlParams.get('payment_intent')
            const orderId = urlParams.get('order_id')
            const paymentMethod = urlParams.get('payment_method')
            
            if (paymentIntentId && orderId) {
                try {
                    const response = await axios.post('/api/payments/stripe/confirm', {
                        order_id: orderId,
                        payment_intent_id: paymentIntentId,
                        payment_method: paymentMethod || 'fpx'
                    })
                    
                    console.log('Payment confirmed on backend:', response.data)
                    // Redirect to home or show success
                    window.location.href = '/home'
                } catch (error) {
                    console.error('Failed to confirm payment on backend:', error)
                }
            }
        } else if (redirectStatus === 'failed') {
            console.error('Payment failed during redirect')
        }
    }
})

// --- Actions ---

const closeModal = () => {
    if (isLoading.value) return; // Prevent closing while processing
    
    // Reset Stripe state
    if (props.paymentMethod === 'card' || props.paymentMethod === 'fpx') {
        stripeInitialized.value = false
        elements.value = null
        stripe.value = null
        paymentIntentId.value = null
        errorMessage.value = ''
        stripeAmount.value = 0
    }
    
    emit('close')
}

const processPayment = async () => {
    // Basic Validation
    if (props.paymentMethod === 'card') {
        if (cardForm.number.length < 19 || !cardForm.expiry || !cardForm.cvv) {
            alert('Please fill in complete card details')
            return
        }
    } else if (props.paymentMethod === 'fpx') {
        if (!walletForm.phone || walletForm.pin.length < 6) {
            alert('Please fill in complete FPX details')
            return
        }
    }

    isLoading.value = true
    
    // Simulate API delay
    setTimeout(() => {
        isLoading.value = false
        // Emit success to parent to handle API call or redirection
        emit('confirm-payment', {
            method: props.paymentMethod,
            details: props.paymentMethod === 'card' ? cardForm : walletForm
        })
    }, 1500)
}
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.2s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0 }
  to { opacity: 1 }
}

/* Stripe Element Styles */
#payment-element {
  min-height: 200px;
}
</style>