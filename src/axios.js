// src/axios.js
import axios from 'axios'
import router from './router'

axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8003/api/'

// Add token from localStorage
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token')
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

// Handle global response errors
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
                    router.push({ name: 'Login' })
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
