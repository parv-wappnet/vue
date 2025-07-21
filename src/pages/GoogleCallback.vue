<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@stores/auth'
import { onMounted } from 'vue'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

onMounted(() => {
  const token = route.query.token
  const user = {
    name: route.query.name,
    email: route.query.email,
    avatar: route.query.avatar,
    avatar_url: route.query.avatar_url,
    id: route.query.id ? parseInt(route.query.id) : null,
  }
  const passwordSet = route.query.password_set === 'true' || route.query.password_set === '1'
  if (token) {
    auth.token = token
    auth.user = user
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    if (passwordSet) {
      auth.setAuth(user, token)
    }
    router.push(passwordSet ? '/profile' : '/set-password')
  } else {
    router.push('/login')
  }
})
</script>

<template>
  <div>Signing in via Google...</div>
</template>
