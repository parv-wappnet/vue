<!-- src/components/ChatWindow.vue -->
<template>
    <div class="chat-container p-4">
        <h3 class="text-lg font-bold mb-4">Conversation: {{ conversationName }}</h3>

        <div class="chat-messages h-80 overflow-y-auto border p-2 mb-4 relative">
            <template v-for="(messageGroup, date) in groupedMessages" :key="date">
                <div class="message-day-group">
                    <div class="date">
                        {{ formatDate(date) }}
                    </div>
                    <ChatMessageItem v-for="message in messageGroup" :key="message.id" :message="message"
                        :currentUserId="currentUserId" />
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
const newMessage = ref('')
const messages = ref([])
const conversationName = ref('Private Chat')
const auth = useAuthStore()
const currentUserId = computed(() => auth.user?.id)
const userId = ref(route.params.userId)
const conversationId = ref(null)
const echo = createEcho(auth.token)

// Group messages by date
const groupedMessages = computed(() => {
    const groups = {}
    messages.value.forEach(message => {
        const date = new Date(message.created_at).toLocaleDateString()
        if (!groups[date]) {
            groups[date] = []
        }
        groups[date].push(message)
    })
    return groups
})

// Format date for display
const formatDate = (dateString) => {
    const date = new Date(dateString)
    const today = new Date()
    const yesterday = new Date(today)
    yesterday.setDate(yesterday.getDate() - 1)

    if (dateString === today.toLocaleDateString()) {
        return 'Today'
    } else if (dateString === yesterday.toLocaleDateString()) {
        return 'Yesterday'
    }
    return dateString
}

// Get or create conversation
const initConversation = async () => {
    try {
        const res = await axios.get(`/conversations/private/${userId.value}`)
        conversationName.value = res.data.name || 'Private Chat2'
        const id = res.data.conversation_id
        conversationId.value = id
        await loadMessages()

        echo.private(`chat.${id}`)
            .listen('.MessageSent', (e) => {
                if (e.sender_id !== auth.user.id) {
                    messages.value.push(e)
                }
            })
    } catch (err) {
        console.error('❌ Failed to get or create conversation:', err)
    }
}

// Load messages
const loadMessages = async () => {
    if (!conversationId.value) return
    try {
        const res = await axios.get(`/conversations/${conversationId.value}/messages`)
        messages.value = res.data.reverse()
    } catch (e) {
        console.error('❌ Failed to load messages:', e)
    }
}

// Send message
const sendMessage = async (messageText) => {
    if (messageText && conversationId.value) {
        try {
            await axios.post(`/conversations/${conversationId.value}/messages`, {
                body: messageText,
            })
            await loadMessages()
        } catch (e) {
            console.error('❌ Failed to send message:', e)
        }
    }
}

onMounted(() => initConversation())
onBeforeUnmount(() => echo.leave(`chat.${conversationId}`))
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

.message-day-group {
    margin-bottom: 1rem;
}

.date {
    text-align: center;
    padding: 0.5rem 0;
    font-size: 0.9rem;
    color: #666;
    background-color: #f9f9f9;
    border-radius: 0.25rem;
    margin: 0.5rem 0;
    position: sticky;
    top: 0;
}
</style>
