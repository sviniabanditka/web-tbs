import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: null,
    user: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    initialize() {
      const token = localStorage.getItem('auth_token')
      if (token) {
        this.setToken(token)
      }
    },
    
    setToken(token) {
      this.token = token
      localStorage.setItem('auth_token', token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    },
    
    clearAuth() {
      this.token = null
      this.user = null
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
    },
    
    async fetchUser() {
      if (!this.token) return
      try {
        const response = await axios.get('/user')
        this.user = response.data
      } catch (error) {
        this.clearAuth()
        throw error
      }
    }
  }
})
