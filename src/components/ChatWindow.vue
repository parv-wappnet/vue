<template>
    <div class="chat-container p-4">
        <h3 class="text-lg font-bold mb-4">Conversation: {{ conversationName }}</h3>

        <div class="chat-messages h-80 overflow-y-auto border p-2 mb-4">
            <div v-for="message in messages" :key="message.id"
                :class="['message', message.sender_id === currentUserId ? 'sent' : 'received']">
                <p>{{ message.content }}</p>
                <span class="text-xs text-gray-500">{{ formatDate(message.created_at) }}</span>
            </div>
        </div>

        <div class="chat-input">
            <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Type a message..."
                class="w-full p-2 border rounded" />
            <button @click="sendMessage" class="mt-2 p-2 bg-blue-500 text-white rounded">Send</button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import axios from '../axios'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { echo } from '../services/echo'
const route = useRoute()
const newMessage = ref('')
const messages = ref([])
const conversationName = ref('Private Chat')
const authStore = useAuthStore()
const currentUserId = computed(() => authStore.user?.id)
const userId = ref(route.params.userId)
const conversationId = ref(null)

// Step 1: Get or create conversation with the clicked user
const initConversation = async () => {
    try {
        const res = await axios.get(`/conversations/private/${userId.value}`)
        conversationId.value = res.data.conversation_id
        await loadMessages()
    } catch (err) {
        console.error('❌ Failed to get or create conversation:', err)
    }
}

// Step 2: Load messages for that conversation
const loadMessages = async () => {
    if (!conversationId.value) return
    try {
        const res = await axios.get(`/conversations/${conversationId.value}/messages`)
        messages.value = res.data.reverse()
        conversationName.value = res.data[0]?.conversation?.name || 'Private Chat'
    } catch (e) {
        console.error('❌ Failed to load messages:', e)
    }
}

// Step 3: Send message
const sendMessage = async () => {
    if (newMessage.value.trim() && conversationId.value) {
        try {
            await axios.post(`/conversations/${conversationId.value}/messages`, {
                body: newMessage.value,
            })
            newMessage.value = ''
            await loadMessages()
        } catch (e) {
            console.error('❌ Failed to send message:', e)
        }
    }
}

const formatDate = (date) =>
    new Date(date).toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' })

onMounted(() => {
    initConversation()
    console.log(conversationId.value)
    echo.channel(`chat`)
        .listen('MessageSent', (e) => {
            initConversation()
            console.log('New message received:', e)
            // messages.value.push(e)
        })
})
</script>

<style scoped>
.chat-container {
    max-width: 600px;
    margin: 0 auto;
}

.chat-messages {
    display: flex;
    flex-direction: column-reverse;
    gap: 0.5rem;
}

.message {
    max-width: 70%;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
}

.sent {
    background-color: #e0f7fa;
    margin-left: auto;
    text-align: right;
}

.received {
    background-color: #f0f0f0;
}

.chat-input input {
    border: 1px solid #ccc;
}

.chat-input button {
    display: block;
    width: 100%;
}
</style>
