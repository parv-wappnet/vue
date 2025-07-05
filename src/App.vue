<!-- src/App.vue -->
<template>
  <div>
    <!-- Show navbar only if user is logged in -->
    <Navbar v-if="auth.user" />
    <router-view />
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import { useAuthStore } from '@stores/auth'

const auth = useAuthStore()
</script>
