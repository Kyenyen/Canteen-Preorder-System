<template>
  <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex justify-between items-center shadow-sm z-50 transition-colors duration-300">
    <!-- Logo / Title -->
    <router-link to="/home" class="flex items-center gap-3 cursor-pointer group">
      <div class="bg-orange-500 text-white p-2 rounded-lg group-hover:bg-orange-600 transition-colors">
        <i class="fas fa-utensils"></i>
      </div>
      <div>
        <h1 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight">UniCanteen</h1>
      </div>
    </router-link>

    <div class="flex items-center gap-4">
      <!-- Dark Mode Toggle (always visible) -->
      <button @click="toggleDarkMode" class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-yellow-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors focus:outline-none">
        <i class="fas fa-moon" v-if="!isDark"></i>
        <i class="fas fa-sun" v-else></i>
      </button>

      <!-- Auth Navigation (only visible if user is logged in) -->
      <div class="flex items-center gap-3" v-if="currentUser">
        <!-- Student Nav Links -->
        <div class="hidden md:flex gap-1 mr-2" v-if="currentUser.role === 'student'">
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

        <!-- Admin Nav Links -->
        <div class="hidden md:flex gap-2 mr-4" v-if="currentUser.role === 'admin'">
          <router-link to="/admin" class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-fire-burner mr-1"></i> Kitchen View
          </router-link>
        </div>

        <!-- User Info + Logout -->
        <div class="flex items-center gap-3 pl-4 border-l border-gray-200 dark:border-gray-700">
          <div class="text-right hidden sm:block">
            <div class="text-sm font-bold text-gray-800 dark:text-white leading-tight">{{ currentUser.username }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ currentUser.role }}</div>
          </div>
          <button @click="logout" class="bg-gray-100 dark:bg-gray-700 hover:bg-red-100 dark:hover:bg-red-900 hover:text-red-600 dark:hover:text-red-300 text-gray-600 dark:text-gray-300 w-8 h-8 rounded-full flex items-center justify-center transition-colors">
            <i class="fas fa-sign-out-alt"></i>
          </button>
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
const currentUser = computed(() => authStore.user) // null if not logged in

function logout() {
  authStore.logout()
  router.push('/')
}
</script>