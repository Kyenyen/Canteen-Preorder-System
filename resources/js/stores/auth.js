import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null, 
    token: localStorage.getItem('token') || null,
  }),
  
  actions: {
    // Register
    async register(payload) {
      return axios.post('/api/register', payload)
    },

  // Login
  async login(payload) {
      const res = await axios.post('/api/login', payload)
      
      this.user = res.data.user
      this.token = res.data.token

      localStorage.setItem('token', this.token)
      localStorage.setItem('user', JSON.stringify(this.user))

      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    },

    // Logout
    logout() {
      this.user = null
      this.token = null
      
      localStorage.removeItem('token')
      localStorage.removeItem('user')

      delete axios.defaults.headers.common['Authorization']
    }
  }
})