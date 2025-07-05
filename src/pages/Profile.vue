<template>
  <div v-if="auth.user">
    <h1>Welcome, {{ auth.user.name }}</h1>
    <img :src="auth.user.avatar" alt="avatar" width="80" />
    <p>{{ auth.user.email }}</p>

    <!-- <router-link to="/chat">Go to Chat</router-link><br /> -->
    <!-- <button @click="auth.logout()">Logout</button> -->
  </div>
  <div v-else>
    <p>Not logged in</p>
    <router-link to="/">Login</router-link>
  </div>
</template>

<script setup>
import { useAuthStore } from '@stores/auth'
const auth = useAuthStore()
</script>
