<template>
  <!-- Modal Backdrop -->
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 fade-in backdrop-blur-sm">
    
    <!-- Modal Content -->
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all scale-100 flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="bg-gray-50 dark:bg-gray-750 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                <i :class="methodIcon" class="mr-2 text-orange-500"></i>
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

            <!-- 2. Card Form View -->
            <div v-if="paymentMethod === 'card'" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Card Number</label>
                    <input type="text" 
                        v-model="cardForm.number"
                        @input="formatCardNumber"
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none dark:bg-gray-700 dark:text-white" 
                        placeholder="0000 0000 0000 0000" 
                        maxlength="19"
                        inputmode="numeric">
                </div>
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Expiry</label>
                        <input type="text" 
                            v-model="cardForm.expiry"
                            @input="formatExpiry"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none dark:bg-gray-700 dark:text-white" 
                            placeholder="MM/YY" 
                            maxlength="5"
                            inputmode="numeric">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">CVV</label>
                        <input type="password" 
                            v-model="cardForm.cvv"
                            @input="onlyNumbers('cvv', 3)"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none dark:bg-gray-700 dark:text-white" 
                            placeholder="***" 
                            maxlength="3"
                            inputmode="numeric">
                    </div>
                </div>
                <button @click="processPayment" :disabled="isLoading" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50">
                    <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                    <i v-else class="fas fa-credit-card"></i>
                    <span>{{ isLoading ? 'Processing...' : 'Pay with Card' }}</span>
                </button>
            </div>

            <!-- 3. eWallet Form View -->
            <div v-if="paymentMethod === 'ewallet'" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Phone Number</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-xl dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            +60
                        </span>
                        <input type="text" 
                            v-model="walletForm.phone"
                            @input="formatPhone"
                            class="rounded-none rounded-r-xl bg-gray-50 border border-gray-300 text-gray-900 focus:ring-orange-500 focus:border-orange-500 block flex-1 min-w-0 w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                            placeholder="12 345 6789"
                            inputmode="numeric">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">6-Digit PIN</label>
                    <input type="password" 
                        v-model="walletForm.pin"
                        @input="onlyNumbersWallet('pin', 6)"
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none dark:bg-gray-700 dark:text-white tracking-widest" 
                        placeholder="******" 
                        maxlength="6" 
                        inputmode="numeric">
                </div>
                <button @click="processPayment" :disabled="isLoading" class="w-full mt-4 bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50">
                    <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                    <i v-else class="fas fa-wallet"></i>
                    <span>{{ isLoading ? 'Processing...' : 'Pay with eWallet' }}</span>
                </button>
            </div>

        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
    isOpen: Boolean,
    paymentMethod: String, // 'card', 'ewallet', 'duitnow'
    amount: Number,
    orderId: Number
})

const emit = defineEmits(['close', 'confirm-payment'])

const isLoading = ref(false)

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
        case 'card': return 'Credit/Debit Card';
        case 'ewallet': return 'eWallet';
        case 'duitnow': return 'DuitNow QR';
        default: return 'Payment';
    }
})

const methodIcon = computed(() => {
    switch(props.paymentMethod) {
        case 'card': return 'fa-credit-card';
        case 'ewallet': return 'fa-wallet';
        case 'duitnow': return 'fa-qrcode';
        default: return 'fa-money-bill';
    }
})

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

// --- Actions ---

const closeModal = () => {
    if (isLoading.value) return; // Prevent closing while processing
    emit('close')
}

const processPayment = async () => {
    // Basic Validation
    if (props.paymentMethod === 'card') {
        if (cardForm.number.length < 19 || !cardForm.expiry || !cardForm.cvv) {
            alert('Please fill in complete card details')
            return
        }
    } else if (props.paymentMethod === 'ewallet') {
        if (!walletForm.phone || walletForm.pin.length < 6) {
            alert('Please fill in complete eWallet details')
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
</style>