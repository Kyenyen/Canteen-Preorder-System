<template>
  <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex justify-between items-center shadow-sm z-50 transition-colors duration-300 relative">
    
    
    <router-link to="/home" class="flex items-center gap-3 cursor-pointer group">
      <div class="bg-orange-500 text-white p-2 rounded-lg group-hover:bg-orange-600 transition-colors">
        <i class="fas fa-utensils"></i>
      </div>
      <div>
        <h1 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight">UniCanteen</h1>
      </div>
    </router-link>

    <div class="flex items-center gap-4">
      
      
      <div class="flex items-center gap-3" v-if="currentUser">
        
        
        <div class="hidden md:flex gap-1 mr-2" v-if="currentUser.role === 'user'">
          <router-link to="/home" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-home mr-1"></i> Home
          </router-link>
          <router-link to="/menu" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-burger mr-1"></i> Menu
          </router-link>
          <router-link to="/history" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-clock-rotate-left mr-1"></i> My Orders
          </router-link>
        </div>

        
        <div class="hidden md:flex gap-2 mr-4" v-if="currentUser.role === 'admin'">
          <router-link to="/admin" class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-box-archive mr-1"></i> Order Management
          </router-link>
          <router-link to="/admin/users" class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-users-cog"></i> User Management
          </router-link>
          <router-link to="/admin/menu" class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-utensils mr-1"></i> Menu Management
          </router-link>
          <router-link to="/admin/categories" class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-tags mr-1"></i> Categories
          </router-link>
          <router-link to="/admin/report" class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-chart-line"></i> Sales Report
          </router-link>
        </div>

        
        <button @click="toggleDarkMode" class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-yellow-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors focus:outline-none">
          <i class="fas fa-moon" v-if="!isDark"></i>
          <i class="fas fa-sun" v-else></i>
        </button>
        
        
        <div class="relative group pl-4 border-l border-gray-200 dark:border-gray-700">
            <button class="flex items-center gap-3 focus:outline-none text-left">
              <div class="text-right hidden sm:block">
                <div class="text-sm font-bold text-gray-800 dark:text-white leading-tight">{{ currentUser.username }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ currentUser.role }}</div>
              </div>
              
              
              <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-gray-700 flex items-center justify-center text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-gray-600 transition-transform group-hover:scale-105 shadow-sm overflow-hidden">
                
                <img v-if="currentUser.photo" :src="getAvatarUrl(currentUser.photo)" alt="User Avatar" class="w-full h-full object-cover">
                
                <i v-else class="fas fa-user"></i>
              </div>
            </button>

            
            <div class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-2 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 transform origin-top-right z-50">
                
                
                <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-gray-700 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                    <i class="fas fa-id-card mr-2 w-5 text-center text-gray-400 group-hover:text-orange-500"></i> My Profile
                </router-link>
                
                
                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                
                <button @click="logout" class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2 w-5 text-center"></i> Log Out
                </button>
            </div>
        </div>

      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../../../js/stores/auth'
import router from '../../../js/router'

// Dark mode toggle
const isDark = ref(false)
function toggleDarkMode() {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
}

// Auth store
const authStore = useAuthStore()
const currentUser = computed(() => authStore.user)

// ... in the <script setup> block

// NEW: Helper function to get full image URL
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

function logout() {
  authStore.logout()
  router.push('/')
}
</script>