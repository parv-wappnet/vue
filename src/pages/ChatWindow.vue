<template>
    <div class="chat-container p-4">
        <h3 class="text-lg font-bold mb-4">Conversation: {{ conversationName }}</h3>

        <div class="chat-messages h-80 overflow-y-auto border p-2 mb-4 relative">
            <template v-for="(message, index) in messages" :key="message.id">
                <!-- Date divider BEFORE first message of a day -->
                <ChatMessageItem :message="message" :currentUserId="currentUserId" />
                <div v-if="showDateDivider(message, index)" class="date">
                    {{ formatDate(message.created_at) }}
                </div>
            </template>
        </div>

        <ChatInputBox @send="sendMessage" />
    </div>
</template>

<script setup>
import { onMounted, ref, computed, onBeforeUnmount } from 'vue'
import axios from '../axios'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@stores/auth'
import { createEcho } from '@services/echo'
import ChatInputBox from '@chat/ChatInputBox.vue'
import ChatMessageItem from '@chat/ChatMessageItem.vue'

const route = useRoute()
const auth = useAuthStore()
const currentUserId = computed(() => auth.user?.id)

const messages = ref([])
const conversationName = ref('Private Chat')
const conversationId = ref(route.params.userId)
const echo = createEcho(auth.token)

// Format the date for the date divider
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Only show date divider before first message of the day
const showDateDivider = (message, index) => {
    if (index === messages.value.length - 1) return true // last message (first in reverse)

    const currentDate = new Date(message.created_at).toDateString()
    const prevDate = new Date(messages.value[index + 1].created_at).toDateString()
    return currentDate !== prevDate
}

// Fetch initial conversation and listen via Echo
const initConversation = async () => {
    try {
        await loadMessages()

        echo.private(`chat.${conversationId.value}`)
            .listen('.MessageSent', (e) => {
                if (e.sender_id !== currentUserId.value) {
                    console.log('📬 New message received:', e)
                    messages.value.unshift(e)
                }
            })
    } catch (err) {
        console.error('❌ Failed to get or create conversation:', err)
    }
}

// Load messages for this conversation
const loadMessages = async () => {
    if (!conversationId.value) return

    try {
        const res = await axios.get(`/conversations/${conversationId.value}/messages`)
        conversationName.value = res?.data?.name || 'Private Chat'
        messages.value = res.data.messages.reverse()
    } catch (e) {
        console.error('❌ Failed to load messages:', e)
    }
}

// Send message
const sendMessage = async (messageText) => {
    if (!messageText || !conversationId.value) return

    try {
        await axios.post(`/conversations/${conversationId.value}/messages`, { body: messageText })
        await loadMessages()
    } catch (e) {
        console.error('❌ Failed to send message:', e)
    }
}

onMounted(() => initConversation())
onBeforeUnmount(() => echo.leave(`chat.${conversationId.value}`))
</script>

<style scoped>
.chat-container {
    max-width: 700px;
    margin: 0 auto;
    height: calc(100vh - 80px);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column-reverse;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    background-color: #fff;
    margin-bottom: 0.5rem;
}

.date {
    text-align: center;
    padding: 0.5rem 0;
    font-size: 0.9rem;
    color: #666;
    /* background-color: #f9f9f9; */
    border-radius: 0.25rem;
    margin: 0.5rem 0;
    position: sticky;
    top: 0;
}
</style>
