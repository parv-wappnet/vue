<template>
    <nav class="navbar">
        <div class="navbar-left">
            <router-link to="/profile" class="navbar-logo">
                💬 <span>ChatApp</span>
            </router-link>
        </div>
        <div class="navbar-right">
            <router-link to="/profile" class="navbar-link">👤 Profile</router-link>
            <router-link to="/group" class="navbar-link">👥 Groups</router-link>
            <router-link to="/chat" class="navbar-link">💭 Chats</router-link>
            <button @click="logout" class="logout-btn">Logout</button>
        </div>
    </nav>
</template>

<script setup>
import { useAuthStore } from '@stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const logout = () => {
    auth.logout()
    router.push('/login')
}
</script>

<style scoped>
.navbar {
    background: linear-gradient(to right, #7e22ce, #db2777, #f97316);
    color: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.navbar-left .navbar-logo {
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    text-decoration: none;
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.navbar-link {
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    text-decoration: none;
    color: white;
    transition: color 0.2s;
}

.navbar-link:hover {
    color: #fef08a;
}

.logout-btn {
    background-color: #ef4444;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.logout-btn:hover {
    background-color: #dc2626;
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
}
</style>
