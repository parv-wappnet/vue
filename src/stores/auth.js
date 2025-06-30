import { defineStore } from 'pinia'
import axios from 'axios'

const API = import.meta.env.VITE_API_BASE_URL

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  actions: {
    loginWithGoogle() {
      window.location.href = `${API}auth/redirect/google`
    },
    async handleGoogleCallback(queryString) {
      const response = await axios.get(`${API}auth/callback/google${queryString}`)
      this.user = response.data.user
      this.token = response.data.token
      localStorage.setItem('token', this.token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`

      // 👇 Redirect based on whether password is set
      if (!this.user.password_set) {
        window.location.href = '/set-password'
      } else {
        window.location.href = '/profile'
      }
    },
    async login({ email, password }) {
      const response = await axios.post(`${API}login`, { email, password })
      this.user = response.data.user
      this.token = response.data.token
      localStorage.setItem('token', this.token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    },
    async register({ name, email, password, password_confirmation }) {
      const response = await axios.post(`${API}register`, {
        name,
        email,
        password,
        password_confirmation
      })
      this.user = response.data.user
      this.token = response.data.token
      localStorage.setItem('token', this.token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    },
    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    }
  }
})
