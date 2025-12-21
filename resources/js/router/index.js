import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Import your views...
import Login from '../../views/canteen/pages/Login.vue'
import Register from '../../views/canteen/pages/Register.vue'
import Home from '../../views/canteen/pages/Home.vue'
import Menu from '../../views/canteen/pages/Menu.vue'
import Checkout from '../../views/canteen/pages/Checkout.vue'
import History from '../../views/canteen/pages/History.vue'
import Admin from '../../views/canteen/pages/Admin.vue'
import ProductManager from '../../views/canteen/pages/ProductManager.vue'
import CategoryAdmin from '../../views/canteen/pages/CategoryManager.vue'
import UserManager from '../../views/canteen/pages/UserManager.vue'
import SalesReport from '../../views/canteen/pages/SalesReport.vue'
import ForgotPassword from '../../views/canteen/pages/ForgotPassword.vue'
import ResetPassword from '../../views/canteen/pages/ResetPassword.vue'
import Profile from '../../views/canteen/pages/Profile.vue'
import PaymentSuccess from '../../views/canteen/pages/PaymentSuccess.vue'
import OrderDetails from '../../views/canteen/pages/OrderDetails.vue'

const routes = [
    {
        path: '/',
        alias: '/login',
        component: Login,
        meta: { guest: true, title: 'Login' }
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
        path: '/checkout',
        component: Checkout,
        meta: { requiresAuth: true, title: 'Checkout' }
    },
    {
        path: '/history',
        component: History,
        meta: { requiresAuth: true, title: 'My Orders' }
    },
    {
        path: '/order/:id',
        component: OrderDetails,
        meta: { requiresAuth: true, title: 'Order Details' }
    },
    {
        path: '/forgot-password',
        component: ForgotPassword,
        meta: { guest: true, title: 'Forgot Password' }
    },
    {
        path: '/reset-password',
        component: ResetPassword,
        meta: { guest: true, title: 'Reset Password' }
    },
    {
        path: '/profile',
        component: Profile,
        meta: { requiresAuth: true, title: 'Profile' }
    },
    {
        path: '/payment-success',
        component: PaymentSuccess,
        meta: { requiresAuth: true, title: 'Payment Processing' }
    },
    {
        path: '/admin',
        component: Admin,
        meta: { requiresAuth: true, title: 'Kitchen Dashboard', role: 'admin' } // Added role: 'admin' for consistency
    },
    {
        path: '/admin/menu',
        component: ProductManager,
        meta: { requiresAuth: true, title: 'Menu Manager', role: 'admin' }
    },
    {
        path: '/admin/categories', // Category Manager <-- NEW ROUTE
        component: CategoryAdmin,
        meta: { requiresAuth: true, title: 'Category Manager', role: 'admin' }
    },
    {
        path: '/admin/report',
        component: SalesReport,
        meta: { requiresAuth: true, title: 'Sales Report', role: 'admin' }
    },
    {
        path: '/admin/users',
        component: UserManager,
        meta: { requiresAuth: true, title: 'User Management', role: 'admin' }
    },
    // Catch-all
    { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const appName = 'UniCanteen';
    document.title = to.meta.title ? `${appName} | ${to.meta.title}` : appName;

    // 2. Existing Auth Logic
    const authStore = useAuthStore()
    const isAuthenticated = !!authStore.token
    const userRole = authStore.user ? authStore.user.role : null 

    if (isAuthenticated && to.meta.guest) {
        if (userRole === 'admin') {
            return next('/admin')
        } else {
            return next('/home')
        }
    }

    if (to.meta.requiresAuth && !isAuthenticated) {
        return next('/')
    }

    // 3. Role-based check: Deny access if a route requires a role (e.g., 'admin') that the user does not have.
    if (to.meta.role && to.meta.role !== userRole) {
        console.warn(`Access denied: User role (${userRole}) cannot access route requiring (${to.meta.role})`)
        return next('/home') 
    }

    next()
})

export default router