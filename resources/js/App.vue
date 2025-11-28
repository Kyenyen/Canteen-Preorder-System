<template>
  <div class="flex flex-col min-h-screen">
    <!-- Header -->
    <HeaderComponent />

    <!-- Main content (current route) -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- Footer -->
    <FooterComponent />
  </div>
</template>

<script setup>
import HeaderComponent from '../views/canteen/partials/Navigation.vue'
import FooterComponent from '../views/canteen/components/Footer.vue'
</script>
