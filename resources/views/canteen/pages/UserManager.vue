<template>
  <div class="min-h-screen w-full flex flex-col fade-in p-6 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Header -->
    <div class="max-w-6xl mx-auto w-full mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">User Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Add, edit, or remove system users.</p>
      </div>
      <button @click="openModal()" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl shadow-lg transition transform active:scale-95 flex items-center gap-2 font-bold" :disabled="loading">
        <i class="fas fa-plus"></i> Add New User
      </button>
    </div>

    <!-- Notification -->
    <div v-if="notification.message" 
        class="max-w-6xl mx-auto w-full mb-4 p-4 border-l-4 rounded-lg shadow-md transition-all duration-300"
        :class="notification.type === 'success' ? 'bg-green-100 border-green-400 text-green-700 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300' : 'bg-red-100 border-red-400 text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300'">
      <p class="font-bold">{{ notification.type.charAt(0).toUpperCase() + notification.type.slice(1) }}:</p>
      <p>{{ notification.message }}</p>
    </div>

    <!-- Users Table -->
    <div class="max-w-6xl mx-auto w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 font-medium border-b border-gray-200 dark:border-gray-700">
            <tr>
              <th class="px-6 py-4">Username</th>
              <th class="px-6 py-4">Email</th>
              <th class="px-6 py-4">Role</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
            <tr v-if="loading">
                <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">Loading users...</td>
            </tr>
            <tr v-else-if="users.length === 0">
                <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No users found.</td>
            </tr>
            <tr v-for="user in users" :key="user.user_id" class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
              
              <!-- Username -->
              <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ user.username }}</td>
              
              <!-- Email -->
              <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ user.email }}</td>
              
              <!-- Role -->
              <td class="px-6 py-4">
                <span :class="user.role === 'admin' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'" class="px-3 py-1 text-xs font-bold rounded-full">
                  {{ user.role === 'admin' ? 'Admin' : 'Student' }}
                </span>
              </td>
              
              <!-- Actions -->
              <td class="px-6 py-4 text-right flex justify-end gap-2">
                <button @click="viewUserOrders(user)" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition" title="View Orders">
                  <i class="fas fa-history"></i>
                </button>
                <button @click="openModal(user)" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="deleteUser(user.user_id, user.username)" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Delete">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 fade-in backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
          {{ isEditing ? 'Edit User' : 'Add New User' }}
        </h3>

        <!-- Modal Error Alert -->
        <div v-if="modalError" class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-start gap-2">
            <i class="fas fa-exclamation-circle mt-0.5"></i>
            <span>{{ modalError }}</span>
        </div>

        <!-- Form -->
        <form @submit.prevent="saveUser" class="space-y-4">
          
          <div>
              <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Username</label>
              <input type="text" v-model="form.username" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
          </div>

          <div>
              <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Email</label>
              <input type="email" v-model="form.email" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
          </div>

          <div>
              <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Role</label>
              <select v-model="form.role" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                <option value="student">Student</option>
                <option value="admin">Admin</option>
              </select>
          </div>

          <div>
              <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Password {{ isEditing ? '(leave blank to keep current)' : '(required)' }}</label>
              <input type="password" v-model="form.password" :required="!isEditing" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button type="button" @click="closeModal" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Cancel</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow-md transition disabled:opacity-50">
                <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
                {{ isEditing ? 'Update User' : 'Create User' }}
            </button>
          </div>
        </form>

      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-sm w-full p-6 transition-all transform scale-100">
        <h3 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">Confirm Deletion</h3>
        <p class="text-gray-700 dark:text-gray-300 mb-6">
          Are you sure you want to delete the user: 
          <span class="font-semibold italic">{{ userToDelete.name }}</span>? 
          This action cannot be undone.
        </p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" :disabled="loading"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition disabled:opacity-50">
            Cancel
          </button>
          <button @click="confirmDelete" :disabled="loading"
            class="px-4 py-2 bg-red-600 text-white font-semibold rounded-xl shadow-md hover:bg-red-700 transition disabled:opacity-50 flex items-center gap-2">
            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- User Orders Modal -->
    <div v-if="showOrdersModal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 fade-in backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 w-full max-w-4xl rounded-2xl shadow-2xl p-6 relative max-h-[90vh] overflow-hidden flex flex-col">
        
        <div class="flex items-center justify-between mb-4 flex-shrink-0">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">
            Order History for <span class="text-orange-600">{{ selectedUser?.username }}</span>
          </h3>
          <button @click="showOrdersModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
          </button>
        </div>

        <!-- Orders Table (Scrollable) -->
        <div class="overflow-x-auto overflow-y-auto flex-1 border border-gray-200 dark:border-gray-700 rounded-lg">
          <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 font-medium border-b border-gray-200 dark:border-gray-700 sticky top-0">
              <tr>
                <th class="px-4 py-3">Order ID</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Payment</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
              <tr v-if="userOrders.length === 0">
                <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No orders found for this user</td>
              </tr>
              <tr v-for="order in userOrders" :key="order.order_id" class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ order.order_id }}</td>
                <td class="px-4 py-3">{{ formatDate(order.date) }}</td>
                <td class="px-4 py-3 font-bold">RM {{ Number(order.total).toFixed(2) }}</td>
                <td class="px-4 py-3">
                  <span :class="getStatusColor(order.status)" class="px-2 py-1 text-xs font-bold rounded-full">
                    {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span v-if="order.payment" :class="order.payment.refunded ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'" class="px-2 py-1 text-xs font-bold rounded-full">
                    {{ order.payment.refunded ? 'Refunded' : order.payment.method }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import '@fortawesome/fontawesome-free/css/all.css'

// State
const users = ref([])
const showModal = ref(false)
const isEditing = ref(false)
const loading = ref(false)
const modalError = ref('')
const notification = reactive({ message: null, type: 'success' })

const showDeleteModal = ref(false)
const userToDelete = reactive({ id: null, name: '' })

const showOrdersModal = ref(false)
const selectedUser = ref(null)
const userOrders = ref([])

const form = reactive({
    id: null,
    username: '',
    email: '',
    role: 'student',
    password: ''
})

// Methods
const showNotification = (message, type = 'success') => {
    notification.message = message
    notification.type = type
    setTimeout(() => {
        notification.message = null
    }, 5000)
}

const fetchUsers = async () => {
    loading.value = true
    try {
        const res = await axios.get('/api/admin/users')
        users.value = res.data
    } catch (err) {
        console.error('Error fetching users:', err)
        showNotification('Failed to load users.', 'error')
    } finally {
        loading.value = false
    }
}

const openModal = (user = null) => {
    modalError.value = ''
    
    if (user) {
        isEditing.value = true
        form.id = user.user_id
        form.username = user.username
        form.email = user.email
        form.role = user.role
        form.password = ''
    } else {
        isEditing.value = false
        form.id = null
        form.username = ''
        form.email = ''
        form.role = 'student'
        form.password = ''
    }
    
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const saveUser = async () => {
    loading.value = true
    modalError.value = ''
    
    try {
        const data = {
            username: form.username,
            email: form.email,
            role: form.role,
        }
        
        if (form.password) {
            data.password = form.password
        }

        let res

        if (isEditing.value) {
            res = await axios.put(`/api/admin/users/${form.id}`, data)
        } else {
            if (!form.password) {
                modalError.value = 'Password is required for new users.'
                loading.value = false
                return
            }
            data.password = form.password
            res = await axios.post('/api/admin/users', data)
        }

        showNotification(res.data.message || (isEditing.value ? 'User updated.' : 'User created.'), 'success')
        await fetchUsers()
        closeModal()
    } catch (err) {
        console.error('Failed to save:', err.response?.data || err)
        
        if (err.response?.data?.errors) {
            modalError.value = Object.values(err.response.data.errors).flat().join('\n')
        } else if (err.response?.data?.message) {
            modalError.value = err.response.data.message
        } else {
            modalError.value = 'Unknown error during user save.'
        }
    } finally {
        loading.value = false
    }
}

const deleteUser = (id, name) => {
    userToDelete.id = id
    userToDelete.name = name
    showDeleteModal.value = true
}

const confirmDelete = async () => {
    const id = userToDelete.id
    loading.value = true
    showDeleteModal.value = false
    
    try {
        const res = await axios.delete(`/api/admin/users/${id}`)
        showNotification(res.data.message || 'User deleted successfully.', 'success')
        await fetchUsers()
    } catch (err) {
        console.error('Failed to delete:', err.response?.data || err)
        const errorMsg = err.response?.data?.message || 'Deletion failed. Check console for details.'
        showNotification(errorMsg, 'error')
    } finally {
        loading.value = false
        userToDelete.id = null
        userToDelete.name = ''
    }
}

const viewUserOrders = async (user) => {
    selectedUser.value = user
    showOrdersModal.value = true
    
    try {
        const res = await axios.get(`/api/admin/users/${user.user_id}/orders`)
        userOrders.value = res.data.orders
    } catch (err) {
        console.error('Error fetching user orders:', err)
        showNotification('Failed to load user orders.', 'error')
    }
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-MY', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getStatusColor = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
        case 'pending':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'
        case 'cancelled':
            return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
        default:
            return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
    }
}

onMounted(() => {
    fetchUsers()
})
</script>

<style scoped>
.fade-in { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
</style>
