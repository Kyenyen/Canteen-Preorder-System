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

    <!-- Cart Component (Global) - Hidden on checkout page -->
    <CartComponent v-if="showCart" ref="cartRef" />

    <!-- Global Notification Component -->
    <Notification
      :show="notificationState.show"
      :type="notificationState.type"
      :title="notificationState.title"
      :message="notificationState.message"
      @close="notificationState.show = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, provide, computed } from 'vue'
import axios from 'axios'
import { useAuthStore } from './stores/auth'
import { useRoute } from 'vue-router'

import HeaderComponent from '../views/canteen/components/Navigation.vue'
import FooterComponent from '../views/canteen/components/Footer.vue'
import CartComponent from '../views/canteen/components/Cart.vue'
import Notification from '../views/canteen/components/Notification.vue'

const authStore = useAuthStore()
const cartRef = ref(null)
const route = useRoute()

// Hide cart on checkout page
const showCart = computed(() => route.path !== '/checkout')

// Global notification state
const notificationState = ref({
  show: false,
  type: 'success',
  title: '',
  message: ''
})

// Provide cart toggle function globally
provide('toggleCart', () => {
  if (cartRef.value) {
    cartRef.value.toggleCart()
  }
})

// Provide notification state globally
provide('notificationState', notificationState)

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