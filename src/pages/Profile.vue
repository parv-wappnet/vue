<template>
  <div class="profile-container">
    <div class="profile-card">
      <div v-if="auth.user" class="profile-content">
        <h1 class="profile-title">Welcome, {{ auth.user.name }}</h1>
        <img :src="auth.user.avatar_url || auth.user.avatar" alt="avatar" class="profile-avatar" />
        <button class="profile-button" @click="triggerFileInput">Edit Avatar</button>
        <input type="file" ref="fileInput" @change="uploadPhoto" style="display: none" />
        <p class="profile-email">{{ auth.user.email }}</p>
        <button class="profile-button" @click="showPasswordModal = true">Change Password</button>

        <div v-if="showPasswordModal" class="modal">
          <input v-model="newPassword" type="password" placeholder="New Password" />
          <input v-model="confirmPassword" type="password" placeholder="Confirm Password" />
          <button @click="updatePassword">Update Password</button>
          <button @click="showPasswordModal = false">Cancel</button>
        </div>
      </div>
      <div v-else class="profile-content">
        <p class="profile-message">Not logged in</p>
        <router-link to="/" class="profile-link">Login</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@stores/auth'
import { ref } from 'vue'
import axios from 'axios'

const auth = useAuthStore()
const fileInput = ref(null)
const newPassword = ref('')
const confirmPassword = ref('')
const showPasswordModal = ref(false)

function triggerFileInput() {
  fileInput.value.click()
}

async function uploadPhoto(e) {
  const formData = new FormData()
  formData.append('photo', e.target.files[0])
  try {
    const response = await axios.post('update-avatar', formData)
    await auth.setAuth(response.data.user, auth.token) // update user while keeping existing token
  } catch (err) {
    console.error(err)
  }
}

async function updatePassword() {
  if (newPassword.value !== confirmPassword.value) return alert('Passwords do not match')
  try {
    await axios.post('update-password', {
      password: newPassword.value,
      password_confirmation: confirmPassword.value,
    })
    alert('Password updated successfully')
    showPasswordModal.value = false
  } catch (err) {
    console.error(err)
  }
}
</script>

<style scoped>
.profile-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #4facfe, #00f2fe);
  padding: 20px;
}

.profile-card {
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.profile-title {
  font-size: 2rem;
  color: #333;
  margin-bottom: 20px;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 15px;
  border: 2px solid #ddd;
}

.profile-email {
  font-size: 1rem;
  color: #555;
  margin-bottom: 20px;
}

.profile-message {
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 20px;
}

.profile-link {
  display: inline-block;
  padding: 10px 20px;
  background: #4facfe;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  font-size: 1rem;
  transition: background-color 0.3s;
}

.profile-link:hover {
  background-color: #00f2fe;
}

.profile-button {
  padding: 10px 20px;
  background: #ff4444;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
  margin: 5px;
}

.profile-button:hover {
  background-color: #cc0000;
}

.modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.modal input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

@media (max-width: 480px) {
  .profile-card {
    padding: 20px;
  }

  .profile-title {
    font-size: 1.5rem;
  }

  .profile-avatar {
    width: 60px;
    height: 60px;
  }

  .profile-email,
  .profile-message {
    font-size: 0.9rem;
  }

  .profile-link,
  .profile-button {
    font-size: 0.9rem;
    padding: 8px 16px;
  }
}
</style>
