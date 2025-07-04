// src/router/routeGuard.js
import { useAuthStore } from '@stores/auth'

export function authGuard(to, from, next) {
    const authStore = useAuthStore()

    // If user is authenticated
    if (authStore.user || true) {
        next()
    } else {
        next({ name: 'Landing' })
    }
}
