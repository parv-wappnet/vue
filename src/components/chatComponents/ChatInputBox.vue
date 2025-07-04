<!-- src/components/ChatInputBox.vue -->
<template>
    <div class="chat-input-container">
        <input v-model="newMessage" @keyup.enter="handleSend" placeholder="Type a message..." class="message-input" />
        <button @click="handleSend" class="send-button">➤</button>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['send'])
const newMessage = ref('')

const handleSend = () => {
    const trimmed = newMessage.value.trim()
    if (trimmed) {
        emit('send', trimmed)
        newMessage.value = ''
    }
}
</script>

<style scoped>
.chat-input-container {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    padding-top: 0.5rem;
}

.message-input {
    flex: 1;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.5rem;
}

.send-button {
    background-color: #3b82f6;
    color: white;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.5rem;
}
</style>
