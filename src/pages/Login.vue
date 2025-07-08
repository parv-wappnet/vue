<template>
  <div class="login-container">
    <div class="login-card">
      <h1 class="login-title">Login</h1>
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="input-group">
          <label for="email">Email</label>
          <div class="input-wrapper">
            <span class="input-icon">✉</span>
            <input v-model="email" id="email" type="email" placeholder="Enter your email" required />
          </div>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input v-model="password" id="password" type="password" placeholder="Enter your password" required />
          </div>
        </div>
        <button type="submit" class="login-button">Login</button>
      </form>
      <hr class="divider" />
      <button @click="auth.loginWithGoogle" class="google-button">Login with Google</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')

const handleLogin = async () => {
  try {
    await auth.login({ email: email.value, password: password.value })
    console.log('Login successful:', auth.user)
    router.push('/profile')
  } catch (err) {
    alert('Login failed')
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #4facfe, #00f2fe);
  padding: 20px;
}

.login-card {
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.login-title {
  font-size: 2rem;
  color: #333;
  margin-bottom: 20px;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.input-group {
  text-align: left;
}

.input-group label {
  display: block;
  font-size: 0.9rem;
  color: #555;
  margin-bottom: 5px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 10px;
  color: #999;
  font-size: 1.2rem;
}

input {
  width: 100%;
  padding: 10px 10px 10px 40px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.3s;
}

input:focus {
  border-color: #4facfe;
}

.login-button {
  padding: 10px;
  background: linear-gradient(90deg, #4facfe, #00f2fe);
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 1rem;
  cursor: pointer;
  transition: opacity 0.3s;
}

.login-button:hover {
  opacity: 0.9;
}

.divider {
  border: 0;
  height: 1px;
  background: #eee;
  margin: 20px 0;
}

.google-button {
  padding: 10px;
  background: #fff;
  color: #d34836;
  border: 1px solid #d34836;
  border-radius: 5px;
  font-size: 1rem;
  cursor: pointer;
  width: 100%;
  transition: background-color 0.3s, color 0.3s;
}

.google-button:hover {
  background-color: #d34836;
  color: white;
}

@media (max-width: 480px) {
  .login-card {
    padding: 20px;
  }

  .login-title {
    font-size: 1.5rem;
  }

  input {
    font-size: 0.9rem;
  }

  .login-button,
  .google-button {
    font-size: 0.9rem;
  }
}
</style>