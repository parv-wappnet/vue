<template>
  <div>
    <h2>Set Your Password</h2>
    <form @submit.prevent="submit">
      <input v-model="password" type="password" placeholder="New Password" required />
      <button type="submit">Set Password</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
const password = ref('')

const submit = async () => {
  try {
    await auth.setPassword(password.value)
    router.push('/profile')
  } catch (err) {
    alert('Failed to set password.')
    console.error(err)
  }
}
</script>
