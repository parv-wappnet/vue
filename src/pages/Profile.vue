<template>
  <div class="profile-container">
    <div class="profile-card">
      <div v-if="auth.user" class="profile-content">
        <h1 class="profile-title">Welcome, {{ auth.user.name }}</h1>
        <img :src="auth.user.avatar.includes('google') ? auth.user.avatar : auth.user.avatar_url" alt="avatar"
          class="profile-avatar" />
        <p class="profile-email">{{ auth.user.email }}</p>
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
const auth = useAuthStore()
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
}

.profile-button:hover {
  background-color: #cc0000;
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