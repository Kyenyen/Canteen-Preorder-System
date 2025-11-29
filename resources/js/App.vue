<template>
  <div class="flex flex-col min-h-screen">
    <!-- Header (Navigation) -->
    <!-- v-if check hides it on Login/Register pages -->
    <HeaderComponent />

    <!-- Main content (current route) -->
    <!-- flex-1 ensures this section expands to fill space, pushing footer down -->
    <main class="flex-1 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
      <router-view />
    </main>

    <!-- Footer -->
    <FooterComponent />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from './stores/auth'

import HeaderComponent from '../views/canteen/components/Navigation.vue'
import FooterComponent from '../views/canteen/components/Footer.vue'

const authStore = useAuthStore()

onMounted(async () => {
  if (authStore.token) {
    try {
      const response = await axios.get('/api/user')
      authStore.user = response.data
      localStorage.setItem('user', JSON.stringify(response.data))
    } catch (error) {
      // If token is invalid (e.g. session expired), log the user out
      if (error.response && error.response.status === 401) {
        authStore.logout()
      }
    }
  }
})
</script>