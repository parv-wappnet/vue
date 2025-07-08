<!-- src/App.vue -->
<template>
  <div style="height: 97dvh; overflow: hidden;">
    <!-- Show navbar only if user is logged in -->
    <Navbar v-if="auth.user" />
    <router-view />
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import { useAuthStore } from '@stores/auth'
import "./style.css"
const auth = useAuthStore()
</script>
