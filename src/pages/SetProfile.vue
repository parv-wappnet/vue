<template>
  <div class="set-password-container">
    <div class="set-password-card">
      <h2 class="set-password-title">Set Your Password</h2>
      <form @submit.prevent="submit" class="set-password-form">
        <div class="input-group">
          <label for="password">New Password</label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input v-model="password" id="password" type="password" placeholder="Enter new password" required />
          </div>
          <input type="file" @change="handleFileUpload" accept="image/*" />

        </div>
        <button type="submit" class="set-password-button">Set Password</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@stores/auth'
import { useRouter } from 'vue-router'
import axios from '@services/axios'

const auth = useAuthStore()
const router = useRouter()
const password = ref('')
const profilePhoto = ref(null)

const handleFileUpload = (e) => {
  profilePhoto.value = e.target.files[0]
}
const submit = async () => {
  const formData = new FormData()
  formData.append('password', password.value)
  formData.append('photo', profilePhoto.value)

  try {
    await axios.post('set-profile', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    router.push('/profile')
  } catch (err) {
    alert('Failed to set profile.')
    console.error(err)
  }
}
</script>

<style scoped>
.set-password-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #4facfe, #00f2fe);
  padding: 20px;
}

.set-password-card {
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.set-password-title {
  font-size: 2rem;
  color: #333;
  margin-bottom: 20px;
}

.set-password-form {
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

.set-password-button {
  padding: 10px;
  background: linear-gradient(90deg, #4facfe, #00f2fe);
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 1rem;
  cursor: pointer;
  transition: opacity 0.3s;
}

.set-password-button:hover {
  opacity: 0.9;
}

@media (max-width: 480px) {
  .set-password-card {
    padding: 20px;
  }

  .set-password-title {
    font-size: 1.5rem;
  }

  input {
    font-size: 0.9rem;
  }

  .set-password-button {
    font-size: 0.9rem;
  }
}
</style>