<template>
  <div class="min-h-screen w-full flex flex-col items-center justify-center fade-in p-4 bg-orange-500 dark:bg-gray-900 transition-colors duration-500">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md transition-colors duration-300">
      
      <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Account</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Join KantinCanteen today</p>
      </div>

      <div v-if="successMessage" class="mb-4 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800/50 text-green-700 dark:text-green-300 text-sm rounded-lg flex items-center gap-2 animate-bounce">
        <i class="fas fa-check-circle"></i>
        <span class="font-bold">{{ successMessage }}</span>
      </div>

      <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-start gap-2 animate-pulse whitespace-pre-line text-left">
        <i class="fas fa-exclamation-circle mt-0.5"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4" novalidate>
        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Username</label>
          <input type="text" v-model="username" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white" :disabled="!!successMessage">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">TARC Email</label>
          
          <div class="flex gap-2">
            <div class="relative w-full">
              <input 
                type="email" 
                v-model="email" 
                placeholder="@student.tarc.edu.my / tarc.edu.my" 
                class="w-full p-3 border rounded-xl outline-none transition disabled:opacity-70 disabled:cursor-not-allowed"
                :class="emailVerified 
                  ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 pr-10' 
                  : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500'" 
                :disabled="emailVerified || (otpSent && timer > 0) || !!successMessage"
              >
              
              <div v-if="emailVerified" class="absolute right-3 top-1/2 -translate-y-1/2 text-green-600 dark:text-green-400 animate-bounce">
                <i class="fas fa-check-circle text-xl"></i>
              </div>
            </div>

            <button 
              v-if="!emailVerified"
              type="button" 
              @click="sendOtp" 
              :disabled="!isValidEmail || loadingOtp || timer > 0"
              class="px-4 py-3 rounded-xl font-bold text-sm transition-all whitespace-nowrap min-w-[100px]"
              :class="(timer > 0 || !isValidEmail || loadingOtp)
                ? 'bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:text-gray-400' 
                : 'bg-orange-600 hover:bg-orange-700 text-white shadow-md active:scale-95'"
            >
              <span v-if="loadingOtp"><i class="fas fa-spinner fa-spin"></i></span>
              <span v-else-if="timer > 0">{{ timer }}s</span>
              <span v-else-if="otpSent">Resend</span>
              <span v-else>Verify</span>
            </button>
          </div>

          <div v-if="otpSent && !emailVerified" class="mt-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 animate-fadeIn">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">We have sent a 6-digit OTP to your email. It will expire in 60 seconds.</p>
            <div class="flex gap-2">
              <input 
                type="text" 
                v-model="otpInput" 
                placeholder="Enter Code" 
                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center tracking-widest font-bold uppercase focus:ring-2 focus:ring-orange-500 outline-none dark:bg-gray-800 dark:text-white"
                maxlength="6"
              >
              <button 
                type="button" 
                @click="verifyOtp" 
                class="bg-gray-900 dark:bg-gray-600 text-white px-4 rounded-lg font-bold text-sm hover:bg-gray-800 dark:hover:bg-gray-500 transition"
              >
                Confirm
              </button>
            </div>
            </div>
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

        <div class="mb-4 flex justify-center">
          <div id="recaptcha-container"></div>
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
import { ref, computed, onMounted, watch } from 'vue' 
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../../js/stores/auth'
import axios from 'axios'

const username = ref('')
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const password_confirmation = ref('')
const errorMessage = ref('')
const successMessage = ref('') 
const loading = ref(false)
const captchaToken = ref(null) 

const otpInput = ref('')
const otpSent = ref(false)
const emailVerified = ref(false)
const timer = ref(0)
const loadingOtp = ref(false)
let timerInterval = null

const router = useRouter()
const authStore = useAuthStore()

watch(email, () => {
  errorMessage.value = ''
  successMessage.value = ''
  if (emailVerified.value) {
    emailVerified.value = false
    otpSent.value = false
    otpInput.value = ''
  }
})

// Password Strength
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

// Toggle Password
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

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

// Is Valid Email
const isValidEmail = computed(() => {
  return /@student\.tarc\.edu\.my$|@tarc\.edu\.my$/i.test(email.value)
})

// Send OTP
const sendOtp = async () => {
  if (!isValidEmail.value) {
    errorMessage.value = "Invalid TARC email format."
    return
  }
  
  loadingOtp.value = true
  errorMessage.value = ''

  try {
    await axios.post('/api/send-otp', { email: email.value })
    
    otpSent.value = true
    startTimer(60) 
    successMessage.value = `OTP sent to ${email.value}`
    setTimeout(() => successMessage.value = '', 3000)

  } catch (err) {
    if (err.response && err.response.data && err.response.data.errors) {
      errorMessage.value = Object.values(err.response.data.errors).flat()[0]
    } else {
      errorMessage.value = err.response?.data?.message || "Failed to send OTP."
    }
    otpSent.value = false 
  } finally {
    loadingOtp.value = false
  }
}

// Verify OTP
const verifyOtp = async () => {
  if (!otpInput.value) return

  try {
    await axios.post('/api/verify-otp', { 
      email: email.value, 
      otp: otpInput.value 
    })

    emailVerified.value = true
    otpSent.value = false
    errorMessage.value = ''
    successMessage.value = "Email verified successfully!"
    setTimeout(() => successMessage.value = '', 3000)
    
  } catch (err) {
    errorMessage.value = err.response?.data?.message || "Invalid or expired OTP."
  }
}

// Start Timer
const startTimer = (seconds) => {
  timer.value = seconds
  clearInterval(timerInterval)
  timerInterval = setInterval(() => {
    if (timer.value > 0) {
      timer.value--
    } else {
      clearInterval(timerInterval)
    }
  }, 1000)
}

// Handle Register
const handleRegister = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (!emailVerified.value) {
    errorMessage.value = 'Please verify your email address first.'
    return
  }
  
  if (!captchaToken.value) {
    errorMessage.value = 'Please complete the CAPTCHA verification.'
    return
  }

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
      password_confirmation: password_confirmation.value,
      'g-recaptcha-response': captchaToken.value 
    })
    
    successMessage.value = 'Registration successful! Redirecting to login...'
    setTimeout(() => { router.push('/') }, 2000)

  } catch (err) {
    if (window.grecaptcha) window.grecaptcha.reset()
    captchaToken.value = null

    if (err.response && err.response.data && err.response.data.errors) {
      errorMessage.value = Object.values(err.response.data.errors).flat().join('\n')
    } else {
      errorMessage.value = err.response?.data?.message || 'Registration failed.'
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