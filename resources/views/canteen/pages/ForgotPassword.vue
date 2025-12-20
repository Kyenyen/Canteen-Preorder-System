<template>
  <div class="min-h-screen w-full flex flex-col items-center justify-center fade-in p-4 bg-orange-500 dark:bg-gray-900 transition-colors duration-500">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md transition-colors duration-300">
      
      <!-- Icon & Title -->
      <div class="text-center mb-6">
        <div class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-inner">
          <i class="fas fa-lock-open"></i>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Forgot Password</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Enter your email to receive a reset link.</p>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="mb-4 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800/50 text-green-700 dark:text-green-300 text-sm rounded-lg flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span>{{ successMessage }}</span>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-center gap-2 animate-pulse">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleReset" class="space-y-5" v-if="!successMessage">
        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">TARC Email Address</label>
          <input 
            type="email" 
            v-model="email" 
            placeholder="@student.tarc.edu.my / @tarc.edu.my" 
            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
            required
          >
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white font-bold py-3 rounded-xl shadow-lg transition transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading"><i class="fas fa-spinner fa-spin mr-2"></i>Sending...</span>
          <span v-else>Send Reset Link</span>
        </button>
      </form>

      <!-- Back to Login -->
      <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        Remembered your password? 
        <router-link to="/" class="text-orange-600 dark:text-orange-400 font-bold hover:underline">Back to Login</router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const handleReset = async () => {
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    // Assuming standard Laravel Sanctum/Fortify route
    await axios.post('/api/forgot-password', { email: email.value })
    successMessage.value = 'We have emailed your password reset link!'
  } catch (err) {
    if (err.response && err.response.data) {
        errorMessage.value = err.response.data.message || 'Unable to send reset link.'
    } else {
        errorMessage.value = 'Network error. Please try again later.'
    }
  } finally {
    loading.value = false
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