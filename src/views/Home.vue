<template>
  <div class="container">
    <h1>Laravel Google Auth</h1>

    <div v-if="!auth.token">
      <button @click="auth.loginWithGoogle()">Login with Google</button>
    </div>

    <div v-else>
      <p>Welcome, {{ auth.user.name }}</p>
      <img :src="auth.user.avatar" alt="Avatar" width="80" />
      <button @click="auth.logout()">Logout</button>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
const auth = useAuthStore()

// Automatically handle the Google callback
if (window.location.pathname.includes('/api/auth/callback/google')) {
  auth.handleGoogleCallback(window.location.search)
    .then(() => window.location.href = '/')
}
</script>

<style scoped>
.container {
  padding: 2rem;
  font-family: sans-serif;
}
</style>
