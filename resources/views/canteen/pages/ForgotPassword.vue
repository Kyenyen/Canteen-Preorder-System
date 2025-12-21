<template>
  <div class="min-h-screen w-full flex flex-col items-center justify-center fade-in p-4 bg-orange-500 dark:bg-gray-900 transition-colors duration-500">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md transition-colors duration-300">
      
      <div class="text-center mb-6">
        <div class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-inner">
          <i :class="successMessage ? 'fas fa-envelope-open-text' : 'fas fa-lock-open'"></i>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
          {{ successMessage ? 'Check Your Email' : 'Forgot Password' }}
        </h2>
      </div>

      <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-center gap-2 animate-pulse">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <form @submit.prevent="handleReset" class="space-y-5" v-if="!successMessage">
        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">TARC Email Address</label>
          <input 
            type="email" 
            v-model="email" 
            placeholder="@student.tarc.edu.my / @tarc.edu.my" 
            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white"
            required
          >
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white font-bold py-3 rounded-xl shadow-lg transition transform active:scale-95 disabled:opacity-50"
        >
          <span v-if="loading"><i class="fas fa-spinner fa-spin mr-2"></i>Sending...</span>
          <span v-else>Send Reset Link</span>
        </button>
      </form>

      <div v-else class="text-center space-y-4">
        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/30 text-green-800 dark:text-green-300 text-sm">
           {{ successMessage }}
        </div>
        
        <div class="pt-2">
          <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Didn't receive the email?</p>
          <button 
            @click="handleReset" 
            :disabled="loading || countdown > 0"
            class="text-sm font-bold text-orange-600 dark:text-orange-400 hover:underline disabled:text-gray-400 disabled:no-underline disabled:cursor-not-allowed"
          >
            <span v-if="countdown > 0">Resend available in {{ countdown }}s</span>
            <span v-else><i class="fas fa-redo-alt mr-1"></i> Resend Reset Link</span>
          </button>
        </div>
      </div>

      <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-6">
        <router-link to="/" class="text-orange-600 dark:text-orange-400 font-bold hover:underline flex items-center justify-center gap-2">
          <i class="fas fa-arrow-left text-xs"></i> Back to Login
        </router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeUnmount } from 'vue'
import axios from 'axios'

const email = ref('')
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const countdown = ref(0)
let timerInterval = null

const startCountdown = () => {
  countdown.value = 60
  timerInterval = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(timerInterval)
    }
  }, 1000)
}

onBeforeUnmount(() => {
  if (timerInterval) clearInterval(timerInterval)
})

const handleReset = async () => {
  loading.value = true
  errorMessage.value = ''
  
  try {
    await axios.post('/api/forgot-password', { email: email.value })
    successMessage.value = 'A fresh reset link has been sent to your inbox!'
    startCountdown()
  } catch (err) {
    if (err.response && err.response.status === 429) {
        errorMessage.value = 'Too many requests. Please wait for 60 seconds before retrying.'
    } else {
        errorMessage.value = err.response?.data?.message || 'Unable to send reset link.'
    }
  } finally {
    loading.value = false
  }
}
</script>