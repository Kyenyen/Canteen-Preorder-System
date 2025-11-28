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

    <div class="max-w-4xl mx-auto w-full grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Left Column: User Card -->
      <div class="md:col-span-1">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 text-center">
            <div class="w-24 h-24 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center mx-auto mb-4 text-orange-600 dark:text-orange-400 text-3xl">
                <i class="fas fa-user"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ user.username }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ user.email }}</p>
            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold uppercase rounded-full tracking-wider">
                {{ user.role }}
            </span>
        </div>
      </div>

      <!-- Right Column: Edit Forms -->
      <div class="md:col-span-2 space-y-6">
        
        <!-- Profile Details -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-id-card text-orange-500"></i> Account Details
            </h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">User ID</label>
                    <input type="text" :value="user.user_id" disabled class="w-full p-3 bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Username</label>
                    <input type="text" v-model="form.username" class="w-full p-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none text-gray-800 dark:text-white transition">
                </div>
                <div class="flex justify-end">
                    <button @click="updateProfile" :disabled="loading" class="bg-gray-900 dark:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-800 dark:hover:bg-gray-500 transition text-sm">
                        Update Info
                    </button>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-lock text-orange-500"></i> Change Password
            </h3>
            
            <div v-if="msg" :class="`mb-4 p-3 rounded-lg text-sm flex items-center gap-2 ${msgType === 'success' ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'}`">
                <i :class="`fas ${msgType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`"></i>
                {{ msg }}
            </div>

            <form @submit.prevent="changePassword" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Current Password</label>
                    <input type="password" v-model="pwd.current" required class="w-full p-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none text-gray-800 dark:text-white transition">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">New Password</label>
                        <input type="password" v-model="pwd.new" required class="w-full p-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none text-gray-800 dark:text-white transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Confirm Password</label>
                        <input type="password" v-model="pwd.confirm" required class="w-full p-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none text-gray-800 dark:text-white transition">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" :disabled="loading" class="bg-orange-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-orange-700 transition text-sm shadow-md">
                        Change Password
                    </button>
                </div>
            </form>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useAuthStore } from '../../../js/stores/auth'
import axios from 'axios'

const authStore = useAuthStore()
const user = computed(() => authStore.user || {})
const loading = ref(false)
const msg = ref('')
const msgType = ref('')

const form = reactive({
    username: user.value.username
})

const pwd = reactive({
    current: '',
    new: '',
    confirm: ''
})

const updateProfile = async () => {
    loading.value = true
    try {
        await axios.put('/api/user/profile', { username: form.username })
        // Update local store
        authStore.user.username = form.username
        showMsg('Profile updated successfully', 'success')
    } catch (err) {
        showMsg('Failed to update profile', 'error')
    } finally {
        loading.value = false
    }
}

const changePassword = async () => {
    if (pwd.new !== pwd.confirm) {
        showMsg('New passwords do not match', 'error')
        return
    }
    
    loading.value = true
    try {
        await axios.put('/api/user/password', {
            current_password: pwd.current,
            password: pwd.new,
            password_confirmation: pwd.confirm
        })
        showMsg('Password changed successfully', 'success')
        pwd.current = ''
        pwd.new = ''
        pwd.confirm = ''
    } catch (err) {
        showMsg(err.response?.data?.message || 'Failed to change password', 'error')
    } finally {
        loading.value = false
    }
}

const showMsg = (message, type) => {
    msg.value = message
    msgType.value = type
    setTimeout(() => { msg.value = '' }, 3000)
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