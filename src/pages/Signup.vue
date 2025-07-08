<template>
    <div class="signup-container">
        <div class="signup-card">
            <h1 class="signup-title">Signup</h1>
            <form @submit.prevent="handleRegister" class="signup-form">
                <div class="input-group">
                    <label for="name">Name</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input v-model="form.name" id="name" type="text" placeholder="Enter your name" required />
                    </div>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <span class="input-icon">✉</span>
                        <input v-model="form.email" id="email" type="email" placeholder="Enter your email" required />
                    </div>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input v-model="form.password" id="password" type="password" placeholder="Enter your password"
                            required />
                    </div>
                </div>
                <div class="input-group">
                    <label for="passwordConfirmation">Confirm Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input v-model="form.passwordConfirmation" id="passwordConfirmation" type="password"
                            placeholder="Confirm your password" required />
                    </div>
                </div>
                <button type="submit" class="signup-button">Signup</button>
            </form>
            <hr class="divider" />
            <button @click="auth.loginWithGoogle()" class="google-button">Signup with Google</button>
            <router-link to="/login" class="login-link">Already have an account? Login</router-link>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@stores/auth'

const auth = useAuthStore()
const router = useRouter()

const form = reactive({
    name: '',
    email: '',
    password: '',
    passwordConfirmation: ''
})

const handleRegister = async () => {
    try {
        await auth.register({
            name: form.name,
            email: form.email,
            password: form.password,
            password_confirmation: form.passwordConfirmation,
        })
        router.push('/profile')
    } catch (err) {
        console.error('❌ Registration failed', err)
        alert('Signup failed. Please check your input or try again.')
    }
}
</script>

<style scoped>
.signup-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    padding: 20px;
}

.signup-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.signup-title {
    font-size: 2rem;
    color: #333;
    margin-bottom: 20px;
}

.signup-form {
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

.signup-button {
    padding: 10px;
    background: linear-gradient(90deg, #4facfe, #00f2fe);
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: opacity 0.3s;
}

.signup-button:hover {
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

.login-link {
    display: block;
    margin-top: 10px;
    color: #4facfe;
    text-decoration: none;
    font-size: 0.9rem;
}

.login-link:hover {
    text-decoration: underline;
}

@media (max-width: 480px) {
    .signup-card {
        padding: 20px;
    }

    .signup-title {
        font-size: 1.5rem;
    }

    input {
        font-size: 0.9rem;
    }

    .signup-button,
    .google-button {
        font-size: 0.9rem;
    }
}
</style>