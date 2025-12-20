<template>
  <div class="min-h-screen w-full flex flex-col items-center justify-center fade-in p-4 bg-orange-500 dark:bg-gray-900 transition-colors duration-500">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md transition-colors duration-300">
      
      <!-- Title -->
      <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Account</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Join UniCanteen today</p>
      </div>

      <!-- Success Message (NEW) -->
      <div v-if="successMessage" class="mb-4 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800/50 text-green-700 dark:text-green-300 text-sm rounded-lg flex items-center gap-2 animate-bounce">
        <i class="fas fa-check-circle"></i>
        <span class="font-bold">{{ successMessage }}</span>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-start gap-2 animate-pulse whitespace-pre-line text-left">
        <i class="fas fa-exclamation-circle mt-0.5"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Register Form -->
      <form @submit.prevent="handleRegister" class="space-y-4" novalidate>
        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Username</label>
          <input type="text" v-model="username" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white" :disabled="!!successMessage">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">TARC Email</label>
          <input type="email" v-model="email" placeholder="@student.tarc.edu.my / @tarc.edu.my" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white" :disabled="!!successMessage">
        </div>

        <div class="relative">
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Password</label>
          <div class="relative">
            <input 
              :type="showPassword ? 'text' : 'password'" 
              v-model="password" 
              class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white pr-10" 
              :disabled="!!successMessage"
            >
            <button 
              type="button" 
              @click="togglePassword" 
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>

          <div v-if="password.length > 0" class="mt-2">
            <div class="flex justify-between items-center mb-1">
              <span class="text-[10px] uppercase font-bold text-gray-500">Strength: {{ passwordStrength.label }}</span>
            </div>
            <div class="h-1.5 w-full bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
              <div 
                class="h-full transition-all duration-500" 
                :class="passwordStrength.color"
                :style="{ width: (passwordStrength.score * 25) + '%' }"
              ></div>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Confirm Password</label>
          <input 
            :type="showPassword ? 'text' : 'password'" 
            v-model="password_confirmation" 
            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white" 
            :disabled="!!successMessage"
          >
        </div>

        <button type="submit" :disabled="loading || !!successMessage" class="w-full bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white font-bold py-3 rounded-xl shadow-lg transition transform active:scale-95 mt-2 disabled:opacity-50 disabled:cursor-not-allowed">
          <span v-if="loading"><i class="fas fa-spinner fa-spin mr-2"></i>Registering...</span>
          <span v-else>Register</span>
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        Already have an account? 
        <router-link to="/" class="text-orange-600 dark:text-orange-400 font-bold hover:underline">Sign In</router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../../js/stores/auth'

const username = ref('')
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const password_confirmation = ref('')
const errorMessage = ref('')
const successMessage = ref('') 
const loading = ref(false)

const router = useRouter()
const authStore = useAuthStore()

const passwordStrength = computed(() => {
  const p = password.value
  if (!p) return { score: 0, label: '', color: 'bg-gray-200' }
  if (p.length < 6) return { score: 1, label: 'Weak', color: 'bg-red-500' }
  
  let score = 0
  if (p.length >= 8) score++
  if (/[A-Z]/.test(p)) score++
  if (/[0-9]/.test(p)) score++
  if (/[^A-Za-z0-9]/.test(p)) score++

  if (score <= 1) return { score: 1, label: 'Weak', color: 'bg-red-500' }
  if (score === 2) return { score: 2, label: 'Fair', color: 'bg-yellow-500' }
  if (score === 3) return { score: 3, label: 'Good', color: 'bg-blue-500' }
  return { score: 4, label: 'Strong', color: 'bg-green-500' }
})

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleRegister = async () => {
  errorMessage.value = ''
  successMessage.value = ''
  if (passwordStrength.value.score < 2) {
    errorMessage.value = 'Please use a stronger password.'
    return
  }
  loading.value = true

  try {
    await authStore.register({
      username: username.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })
    
    // Show success message
    successMessage.value = 'Registration successful! Redirecting to login...'
    
    // Wait 2 seconds so user can read message, then redirect
    setTimeout(() => {
        router.push('/') 
    }, 2000)

  } catch (err) {
    console.log(err.response?.data)
    if (err.response && err.response.data && err.response.data.errors) {
      errorMessage.value = Object.values(err.response.data.errors).flat().join('\n')
    } else if (err.response && err.response.data && err.response.data.message) {
      errorMessage.value = err.response.data.message
    } else {
      errorMessage.value = 'Registration failed. Please try again.'
    }
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