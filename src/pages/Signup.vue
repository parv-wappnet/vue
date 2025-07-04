<template>
    <div>
        <h1>Signup</h1>
        <form @submit.prevent="handleRegister">
            <input v-model="form.name" type="text" placeholder="Name" required />
            <input v-model="form.email" type="email" placeholder="Email" required />
            <input v-model="form.password" type="password" placeholder="Password" required />
            <input v-model="form.passwordConfirmation" type="password" placeholder="Confirm Password" required />
            <button type="submit">Signup</button>
        </form>

        <hr />
        <button @click="auth.loginWithGoogle()">Signup with Google</button>
        <router-link to="/login">Already have an account? Login</router-link>
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
