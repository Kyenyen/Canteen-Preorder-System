<template>
  <div class="min-h-screen w-full flex flex-col fade-in p-6 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Header -->
    <div class="max-w-4xl mx-auto w-full mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">My Profile</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Manage your account settings</p>
      </div>
      <router-link to="/home" class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        <i class="fas fa-arrow-left mr-2"></i> Back to Home
      </router-link>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto w-full grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Left Column: User Card -->
      <div class="md:col-span-1">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 text-center">
            
            <!-- Profile Photo Upload -->
            <div class="relative group mx-auto mb-4 w-28 h-28 cursor-pointer" @click="triggerFileInput">
                <div class="w-full h-full rounded-full overflow-hidden border-4 border-white dark:border-gray-700 shadow-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <img v-if="previewUrl || (user && user.photo)" :src="previewUrl || getAvatarUrl(user.photo)" class="w-full h-full object-cover">
                    <i v-else class="fas fa-user text-orange-600 dark:text-orange-400 text-4xl"></i>
                </div>
                <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <i class="fas fa-camera text-white text-xl"></i>
                </div>
            </div>
            
            <input type="file" ref="fileInput" @change="handleFileChange" class="hidden" accept="image/*">

            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ user?.username || 'User' }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ user?.email || '' }}</p>
            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold uppercase rounded-full tracking-wider">
                {{ user?.role || 'User' }}
            </span>
        </div>
      </div>

      <!-- Right Column: Edit Forms -->
      <div class="md:col-span-2 space-y-6">
        
        <!-- 1. Profile Details Form -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-id-card text-orange-500"></i> Account Details
            </h3>

            <!-- Profile Messages -->
            <div v-if="profileMsg" :class="`mb-4 p-3 rounded-lg text-sm flex items-start gap-2 whitespace-pre-line ${profileMsgType === 'success' ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'}`">
                <i :class="`fas ${profileMsgType === 'success' ? 'fa-check-circle mt-1' : 'fa-exclamation-circle mt-1'}`"></i>
                <span>{{ profileMsg }}</span>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">User ID</label>
                    <input type="text" :value="user?.user_id" disabled class="w-full p-3 bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Username</label>
                    <input type="text" v-model="form.username" class="w-full p-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none text-gray-800 dark:text-white transition">
                </div>
                <div class="flex justify-end">
                    <button type="button" @click="updateProfile" :disabled="profileLoading" class="bg-gray-900 dark:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-800 dark:hover:bg-gray-500 transition text-sm flex items-center gap-2 disabled:opacity-50">
                        <i v-if="profileLoading" class="fas fa-spinner fa-spin"></i>
                        <span>{{ profileLoading ? 'Updating...' : 'Update Info' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-lock text-orange-500"></i> Change Password
            </h3>
            
            <div v-if="passwordMsg" :class="`mb-4 p-3 rounded-lg text-sm flex items-start gap-2 whitespace-pre-line ${passwordMsgType === 'success' ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'}`">
                <i :class="`fas ${passwordMsgType === 'success' ? 'fa-check-circle mt-1' : 'fa-exclamation-circle mt-1'}`"></i>
                <span>{{ passwordMsg }}</span>
            </div>

            <form @submit.prevent="changePassword" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Current Password</label>
                    <div class="relative">
                        <input 
                            :type="visibility.current ? 'text' : 'password'" 
                            v-model="pwd.current" 
                            required 
                            class="w-full p-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none text-gray-800 dark:text-white transition pr-10"
                        >
                        <button type="button" @click="toggleVisibility('current')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors">
                            <i :class="visibility.current ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">New Password</label>
                        <div class="relative">
                            <input 
                                :type="visibility.new ? 'text' : 'password'" 
                                v-model="pwd.new" 
                                required 
                                class="w-full p-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none text-gray-800 dark:text-white transition pr-10"
                            >
                            <button type="button" @click="toggleVisibility('new')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors">
                                <i :class="visibility.new ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        
                        <div v-if="pwd.new.length > 0" class="mt-2">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-[10px] uppercase font-bold text-gray-500">Strength: {{ passwordStrength.label }}</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                <div class="h-full transition-all duration-500" 
                                    :class="passwordStrength.color"
                                    :style="{ width: (passwordStrength.score * 25) + '%' }">
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-xs text-gray-400 mt-1">Min. 6 characters</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Confirm Password</label>
                        <div class="relative">
                            <input 
                                :type="visibility.confirm ? 'text' : 'password'" 
                                v-model="pwd.confirm" 
                                required 
                                class="w-full p-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none text-gray-800 dark:text-white transition pr-10"
                            >
                            <button type="button" @click="toggleVisibility('confirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors">
                                <i :class="visibility.confirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" :disabled="passwordLoading" class="bg-orange-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-orange-700 transition text-sm shadow-md flex items-center gap-2 disabled:opacity-50">
                        <i v-if="passwordLoading" class="fas fa-spinner fa-spin"></i>
                        <span>Change Password</span>
                    </button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue' 
import { useAuthStore } from '../../../js/stores/auth'
import axios from 'axios'

const authStore = useAuthStore()
const user = computed(() => authStore.user || {})

const profileLoading = ref(false)
const passwordLoading = ref(false)

const profileMsg = ref('')
const profileMsgType = ref('')
const passwordMsg = ref('')
const passwordMsgType = ref('')

const fileInput = ref(null)
const previewUrl = ref(null)
const selectedFile = ref(null)

const form = reactive({
    username: user.value?.username || ''
})

const pwd = reactive({
    current: '',
    new: '',
    confirm: ''
})

const visibility = reactive({
    current: false,
    new: false,
    confirm: false
})

const toggleVisibility = (field) => {
    visibility[field] = !visibility[field]
}

const passwordStrength = computed(() => {
  const p = pwd.new
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

onMounted(async () => {
    try {
        const response = await axios.get('/api/user')
        authStore.user = response.data
        form.username = response.data.username
    } catch (err) {
        console.error("Failed to fetch fresh profile data.", err)
    }
})

const getAvatarUrl = (path) => {
    if (!path) return null
    if (path.startsWith('http')) {
        return path
    }

    if (path.includes('photos/')) {
        return `/${path}` 
    }
    
    return `/storage/${path}`
}

const triggerFileInput = () => {
    fileInput.value.click()
}

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        selectedFile.value = file
        previewUrl.value = URL.createObjectURL(file)
    }
}

const getErrorMessage = (err, defaultMsg) => {
    if (err.response && err.response.data && err.response.data.errors) {
        return Object.values(err.response.data.errors).flat().join('\n');
    }
    return err.response?.data?.message || defaultMsg;
}

const updateProfile = async () => {
    profileLoading.value = true
    profileMsg.value = '' 
    
    try {
        const formData = new FormData()
        formData.append('username', form.username)
        if (selectedFile.value) {
            formData.append('photo', selectedFile.value)
        }

        const response = await axios.post('/api/user/profile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        authStore.user = response.data.user
        showMsg('profile', 'Profile updated successfully', 'success')
    } catch (err) {
        console.error(err)
        const message = getErrorMessage(err, 'Failed to update profile');
        showMsg('profile', message, 'error')
    } finally {
        profileLoading.value = false
    }
}

const changePassword = async () => {
    passwordMsg.value = '' 

    if (pwd.new && passwordStrength.value.score < 2) {
        showMsg('password', 'Please use a stronger password.', 'error')
        return
    }
    
    passwordLoading.value = true
    try {
        await axios.put('/api/user/password', {
            current_password: pwd.current,
            password: pwd.new,
            password_confirmation: pwd.confirm
        })
        showMsg('password', 'Password changed successfully', 'success')
        
        pwd.current = ''
        pwd.new = ''
        pwd.confirm = ''
    } catch (err) {
        console.error(err)
        const message = getErrorMessage(err, 'Failed to change password');
        showMsg('password', message, 'error')
    } finally {
        passwordLoading.value = false
    }
}

const showMsg = (section, message, type) => {
    if (section === 'profile') {
        profileMsg.value = message
        profileMsgType.value = type
        if(type === 'success') setTimeout(() => { profileMsg.value = '' }, 3000)
    } else {
        passwordMsg.value = message
        passwordMsgType.value = type
        if(type === 'success') setTimeout(() => { passwordMsg.value = '' }, 3000)
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