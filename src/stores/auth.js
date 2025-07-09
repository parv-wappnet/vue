// File: stores/auth.js
// Purpose: Manages authentication state and operations using Pinia store, including user login, registration, and Google OAuth

import { defineStore } from 'pinia'
import axios from '@services/axios'

const baseurl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8003/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
  }),

  persist: {
    storage: localStorage, // Optional: defaults to localStorage
  },

  actions: {
    /**
     * Initiates Google OAuth login flow by redirecting to Google auth URL
     * Arguments: None
     * Returns: void
     * Purpose: Redirects user to Google login page to start OAuth process
     */
    loginWithGoogle() {
      window.location.href = `${baseurl}auth/redirect/google`
    },

    /**
     * Handles regular email/password login
     * Arguments: {email: string, password: string}
     * Returns: Promise<void>
     * Purpose: Authenticates user with email/password and sets auth state
     */
    async login({ email, password }) {
      const res = await axios.post('login', { email, password })
      this.setAuth(res.data.user, res.data.token)
    },



    /**Sets a new password for the authenticated user
     * Arguments: password - New password string
     * Returns: Promise<void>
     * Purpose: Updates user's password in the backend
     */
    // async setProfile(password) {
    //   const res = await axios.post('user/set-password', { password:password })
    //   this.setAuth(res.data.user, res.data.token)
    // },

    /**
     * Registers a new user
     * Arguments: {name: string, email: string, password: string, password_confirmation: string}
     * Returns: Promise<void>
     * Purpose: Creates new user account and sets auth state
     */
    async register({ name, email, password, password_confirmation }) {
      const res = await axios.post('register', {
        name,
        email,
        password,
        password_confirmation,
      })
      this.setAuth(res.data.user, res.data.token)
    },

    /**
     * Sets authentication state
     * Arguments: user - User object, token - Authentication token string
     * Returns: void
     * Purpose: Updates store with user data and authentication token
     */
    setAuth(user, token) {
      this.user = user
      this.token = token
    },

    /**
     * Logs out current user
     * Arguments: None
     * Returns: void
     * Purpose: Clears authentication state
     */
    logout() {
      this.user = null
      this.token = null
    },
  },
})
