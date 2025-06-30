import { defineStore } from 'pinia'
import axios from '../axios' // use the configured axios instance

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  actions: {
    loginWithGoogle() {
      window.location.href = 'auth/redirect/google' // baseURL will be used
    },

    async handleGoogleCallback(queryString) {
      const res = await axios.get(`auth/callback/google${queryString}`)
      this.setAuth(res.data.user, res.data.token)

      if (!res.data.user.password_set) {
        window.location.href = '/set-password'
      } else {
        window.location.href = '/profile'
      }
    },

    async login({ email, password }) {
      console.log('Logging in with:', { email, password })
      const res = await axios.post('login', { email, password })
      this.setAuth(res.data.user, res.data.token)
    },

    async register({ name, email, password, password_confirmation }) {
      const res = await axios.post('register', {
        name,
        email,
        password,
        password_confirmation
      })
      this.setAuth(res.data.user, res.data.token)
    },

    setAuth(user, token) {
      this.user = user
      this.token = token
      localStorage.setItem('token', token)
    },

    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('token')
    }
  }
})
