<template>
  <div class="landing">
    <nav>
      <router-link to="/">About Us</router-link>
      <router-link to="/signup">Signup</router-link>
      <router-link to="/login">Login</router-link>
    </nav>

    <section class="hero">
      <h1>Welcome to ChatApp</h1>
      <p>Connect. Chat. Collaborate.</p>
    </section>
  </div>
</template>

<script setup>
import { useAuthStore } from '@stores/auth'
const auth = useAuthStore()
</script>

<style scoped>
nav {
  display: flex;
  justify-content: space-between;
  padding: 1rem;
  background: #f0f0f0;
}

nav a,
nav button {
  margin: 0 1rem;
}

.hero {
  text-align: center;
  margin-top: 3rem;
}
</style>
