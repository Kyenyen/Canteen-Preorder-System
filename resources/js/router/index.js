import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Import your views...
import Login from '../../views/canteen/pages/Login.vue'
import Register from '../../views/canteen/pages/Register.vue'
import Home from '../../views/canteen/pages/Home.vue'
import Menu from '../../views/canteen/pages/Menu.vue'
import History from '../../views/canteen/pages/History.vue'
import Admin from '../../views/canteen/pages/Admin.vue' // Kitchen Dashboard

const routes = [
  { 
    path: '/', 
    alias: '/login', 
    component: Login, 
    meta: { guest: true, title: 'Login' } // Changed to just the page name
  },
  { 
    path: '/register', 
    component: Register, 
    meta: { guest: true, title: 'Register' } 
  },
  { 
    path: '/home', 
    component: Home, 
    meta: { requiresAuth: true, title: 'Home' } 
  },
  { 
    path: '/menu', 
    component: Menu, 
    meta: { requiresAuth: true, title: 'Menu' } 
  },
  { 
    path: '/history', 
    component: History, 
    meta: { requiresAuth: true, title: 'My Orders' } 
  },
  { 
    path: '/admin', 
    component: Admin, 
    meta: { requiresAuth: true, title: 'Kitchen Dashboard' } 
  },
  // Catch-all
  { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  // 1. Update the Tab Title dynamically
  const appName = 'UniCanteen';
  // If the route has a title, format it as "UniCanteen | PageName", otherwise just "UniCanteen"
  document.title = to.meta.title ? `${appName} | ${to.meta.title}` : appName;

  // 2. Existing Auth Logic
  const authStore = useAuthStore()
  const isAuthenticated = !!authStore.token

  if (isAuthenticated && to.meta.guest) {
    if (authStore.user && authStore.user.role === 'admin') {
        return next('/admin')
    } else {
        return next('/home')
    }
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    return next('/')
  }

  next()
})

export default router