// src/services/axios.js
// This file configures Axios for making HTTP requests throughout the application
// It sets up request/response interceptors and handles global error responses
// Manages authentication token and API base URL configuration

import axios from 'axios'
import router from '@router'
import { useAuthStore } from '@stores/auth'

// Set the base URL for all API requests from environment variables
// Falls back to localhost if not specified
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8003/api/'

/**
 * Request interceptor that adds the authentication token to requests
 * Gets token from localStorage and adds it to the Authorization header
 */
axios.interceptors.request.use(config => {
    const token = useAuthStore().token || localStorage.getItem('token')
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

/**
 * Response interceptor to handle common API error scenarios
 * Manages:
 * - Network errors
 * - 401 Unauthorized errors (session expiry)
 * - 422 Validation errors
 * - 500 Server errors
 * - Other unexpected errors
 */
axios.interceptors.response.use(
    response => response,
    error => {
        const { response } = error

        if (!response) {
            alert('Network error')
        } else {
            switch (response.status) {
                case 401:
                    alert('Session expired. Please login.')
                    localStorage.removeItem('token')
                    router.push({ name: 'Landing' })
                    break
                case 422:
                    const errors = response.data.errors
                    if (errors) alert(Object.values(errors).flat().join('\n'))
                    break
                case 500:
                    alert('Server error. Try again later.')
                    break
                default:
                    alert(response.data.message || 'Unexpected error')
            }
        }

        return Promise.reject(error)
    }
)

export default axios
