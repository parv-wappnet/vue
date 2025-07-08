<template>
    <div class="chat-container">
        <div class="chat-card">
            <h3 class="chat-title">Conversation: {{ conversationName }}</h3>
            <div ref="chatContainer" @scroll="handleScroll" class="chat-messages">
                <template v-for="(message, index) in messages" :key="message.id">
                    <!-- Date divider BEFORE first message of a day -->
                    <ChatMessageItem :message="message" :currentUserId="currentUserId" />
                    <div v-if="showDateDivider(message, index)" class="date-divider">
                        {{ formatDate(message.created_at) }}
                    </div>
                </template>
                <div v-if="loading" class="loading-indicator">Loading...</div>
            </div>
            <ChatInputBox @send="sendMessage" />
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed, onBeforeUnmount, nextTick } from 'vue'
import axios from '../axios'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@stores/auth'
import { createEcho } from '@services/echo'
import ChatInputBox from '@chat/ChatInputBox.vue'
import ChatMessageItem from '@chat/ChatMessageItem.vue'

const route = useRoute()
const auth = useAuthStore()
const currentUserId = computed(() => auth.user?.id)
const conversationId = ref(route.params.conversationId)
const conversationName = ref('Private Chat')

const messages = ref([])
const offset = ref(0)
const limit = 20
const hasMore = ref(true)
const loading = ref(false)

const echo = createEcho(auth.token)
const chatContainer = ref(null)

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'Asia/Kolkata'
    })
}

// Only show date divider before first message of the day
const showDateDivider = (message, index) => {
    if (index === messages.value.length - 1) return true // last message (first in reverse)

    const currentDate = new Date(message.created_at).toDateString()
    const prevDate = new Date(messages.value[index + 1].created_at).toDateString()
    return currentDate !== prevDate
}

const handleScroll = () => {
    const el = chatContainer.value
    if (!el || loading.value || !hasMore.value) return
    const scrollPercentage = (el.scrollTop / (el.scrollHeight - el.clientHeight)) * 100
    if (scrollPercentage <= 10) {
        loadMessages()
    }
}

const initConversation = async () => {
    try {
        await loadMessages()
        await scrollToBottom()

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

const scrollToBottom = async () => {
    await nextTick()
    const el = chatContainer.value
    if (el) {
        el.scrollTop = el.scrollHeight
    }
}

const loadMessages = async () => {
    loading.value = true
    try {
        const res = await axios.get(`/conversations/${conversationId.value}/messages`, {
            params: { offset: offset.value, limit }
        })

        const newMessages = res.data.messages
        if (newMessages.length < limit) {
            hasMore.value = false
        }
        messages.value.push(...newMessages)

        conversationName.value = res.data.name || 'Private Chat'
        offset.value += limit
    } catch (err) {
        console.error('❌ Failed to load messages:', err)
    } finally {
        loading.value = false
    }
}

const sendMessage = async (messageText) => {
    if (!messageText || !conversationId.value) return
    try {
        await axios.post(`/conversations/${conversationId.value}/messages`, {
            body: messageText
        })
        await scrollToBottom()
        messages.value.unshift({
            id: Date.now(), // Temporary ID until server response
            content: messageText,
            sender_id: currentUserId.value,
            conversation_id: conversationId.value,
            type: 'text',
            created_at: new Date().toISOString(),
            sender: {
                id: currentUserId.value,
                name: auth.user?.name,
                email: auth.user?.email
            }
        })
    } catch (e) {
        console.error('❌ Failed to send message:', e)
    }
}

onMounted(() => {
    initConversation()
    chatContainer.value?.addEventListener('scroll', handleScroll)
})

onBeforeUnmount(() => {
    echo.leave(`chat.${conversationId.value}`)
    chatContainer.value?.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.chat-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    padding: 10px;
}

.chat-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 700px;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 10px);
}

.chat-title {
    font-size: 2rem;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column-reverse;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    background-color: #f9f9f9;
    margin-bottom: 1rem;
}

.date-divider {
    text-align: center;
    padding: 0.5rem 0;
    font-size: 0.9rem;
    color: #666;
    background-color: #e0e0e0;
    border-radius: 0.25rem;
    margin: 0.5rem 0;
}

.loading-indicator {
    text-align: center;
    padding: 0.5rem;
    color: #666;
    font-size: 0.9rem;
}

@media (max-width: 480px) {
    .chat-card {
        padding: 20px;
        height: calc(100vh - 60px);
    }

    .chat-title {
        font-size: 1.5rem;
    }

    .chat-messages {
        padding: 0.5rem;
    }

    .date-divider,
    .loading-indicator {
        font-size: 0.8rem;
    }
}
</style>