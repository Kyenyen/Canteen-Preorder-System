<template>
  <div class="min-h-screen w-full flex flex-col items-center justify-center fade-in p-4 bg-orange-500 dark:bg-gray-900 transition-colors duration-500">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md transition-colors duration-300">
      
      <!-- Header -->
      <div class="text-center mb-6">
        <div class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-inner">
          <i class="fas fa-key"></i>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Change Password</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Create a new secure password for your account.</p>
      </div>

      <!-- Messages -->
      <div v-if="successMessage" class="mb-4 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800/50 text-green-700 dark:text-green-300 text-sm rounded-lg flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span>{{ successMessage }}</span>
      </div>

      <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-center gap-2 animate-pulse">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleChangePassword" class="space-y-5" v-if="!successMessage">
        <!-- Hidden Email Field (Visual only, usually readonly) -->
        <div>
          <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Email Address</label>
          <input 
            type="email" 
            :value="email" 
            disabled
            class="w-full p-3 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed"
          >
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">New Password</label>
          <input 
            type="password" 
            v-model="password" 
            placeholder="Min. 6 characters"
            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white"
            required
          >
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Confirm Password</label>
          <input 
            type="password" 
            v-model="passwordConfirmation" 
            placeholder="Re-enter password"
            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white"
            required
          >
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white font-bold py-3 rounded-xl shadow-lg transition transform active:scale-95 disabled:opacity-50"
        >
          <span v-if="loading"><i class="fas fa-spinner fa-spin mr-2"></i>Changing...</span>
          <span v-else>Change Password</span>
        </button>
      </form>

      <!-- Back to Login Link (Shown after success) -->
      <div v-if="successMessage" class="mt-6">
        <router-link to="/login" class="block w-full text-center bg-gray-900 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-gray-800 transition">
          Back to Login
        </router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const email = ref('')
const token = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

onMounted(() => {
  // Extract token and email from the URL query parameters
  token.value = route.query.token || ''
  email.value = route.query.email || ''

  if (!token.value) {
    errorMessage.value = "Invalid password change link."
  }
})

const handleChangePassword = async () => {
  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = "Passwords do not match."
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    // Send the data to the backend
    await axios.post('/api/reset-password', {
      token: token.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    successMessage.value = "Password has been successfully changed!"
    
    // Optional: Redirect automatically after a few seconds
    setTimeout(() => {
        router.push('/login')
    }, 3000)

  } catch (err) {
    if (err.response && err.response.data) {
        // Handle Laravel validation errors or generic messages
        errorMessage.value = err.response.data.message || 
                             (err.response.data.errors ? Object.values(err.response.data.errors).flat().join('\n') : 'Change password failed.')
    } else {
        errorMessage.value = 'An error occurred. Please try again.'
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