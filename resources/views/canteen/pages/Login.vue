<template>
  <div class="min-h-screen w-full flex flex-col items-center justify-center fade-in p-4 bg-orange-500 dark:bg-gray-900 transition-colors duration-500">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md transition-colors duration-300">
      
      <!-- Logo & Title -->
      <div class="text-center mb-6">
        <div class="w-20 h-20 flex items-center justify-center mx-auto mb-4">
          <img 
            :src="'/photos/logo.png'" 
            alt="KantinCanteen Logo" 
            class="w-full h-full object-contain"
          >
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">KantinCanteen</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-2">Skip the queue, eat better.</p>
      </div>

      <!-- Login Error Message -->
      <!-- Added whitespace-pre-line to properly display multiple errors on new lines -->
      <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-start gap-2 animate-pulse whitespace-pre-line text-left">
        <i class="fas fa-exclamation-circle mt-0.5"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-5" novalidate>
        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">TARC Email Address</label>
          <input 
            type="email" 
            v-model="email" 
            placeholder="@student.tarc.edu.my / @tarc.edu.my" 
            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
          >
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Password</label>
          <div class="relative"> <input 
              :type="showPassword ? 'text' : 'password'" 
              v-model="password" 
              class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white pr-12"
            >
            <button 
              type="button" 
              @click="togglePassword" 
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors px-2"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          
          <div class="flex justify-between items-center mt-1">
            <router-link to="/forgot-password" class="text-xs text-orange-600 dark:text-orange-400 underline hover:text-orange-700 dark:hover:text-orange-300 transition-colors font-bold">
              Forgot Password?
            </router-link>
            <p class="text-xs text-gray-400 dark:text-gray-500 text-right">Min. 6 characters</p>
          </div>
        </div>

        <div class="mb-4 flex justify-center">
          <div id="recaptcha-container"></div>
        </div>

        <button type="submit" :disabled="loading" class="w-full bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 text-lg flex justify-center items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
          <i v-if="loading" class="fas fa-spinner fa-spin"></i>
          <span>{{ loading ? 'Signing In...' : 'Sign In' }}</span>
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        Don't have an account? 
        <router-link to="/register" class="text-orange-600 dark:text-orange-400 font-bold hover:underline">Sign Up</router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../../js/stores/auth'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const errorMessage = ref('')
const loading = ref(false)
const captchaToken = ref(null)

const authStore = useAuthStore()
const router = useRouter()

onMounted(() => {
  const checkGrecaptcha = setInterval(() => {
    if (window.grecaptcha) {
      window.grecaptcha.render('recaptcha-container', {
        'sitekey': '6LctkzIsAAAAAJupz9sg4Ar45Azj6cb5Pdv4vg2n', 
        'callback': (token) => {
          captchaToken.value = token
          errorMessage.value = ''
        },
        'expired-callback': () => {
          captchaToken.value = null
        }
      });
      clearInterval(checkGrecaptcha);
    }
  }, 500);
});

// Toggle Password
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

// Handle Login
const handleLogin = async () => {
  errorMessage.value = ''

  if (!captchaToken.value) {
    errorMessage.value = 'Please complete the Captcha verification.'
    return
  }

  loading.value = true

  try {
    await authStore.login({
      email: email.value,
      password: password.value,
      'g-recaptcha-response': captchaToken.value 
    })
    
    if (authStore.user?.role === 'admin') {
        router.push('/admin')
    } else {
        router.push('/home')
    }

  } catch (err) {
    if (window.grecaptcha) window.grecaptcha.reset()
    captchaToken.value = null
  
    if (err.response && err.response.data && err.response.data.errors) {
      errorMessage.value = Object.values(err.response.data.errors).flat().join('\n')
    } else if (err.response && err.response.data && err.response.data.message) {
      errorMessage.value = err.response.data.message
    } else {
      errorMessage.value = 'Login failed. Please check your credentials.'
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