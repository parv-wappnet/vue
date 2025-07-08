<template>
    <div class="chat-container p-4">
        <h3 class="text-lg font-bold mb-4">Conversation: {{ conversationName }}</h3>
        <div ref="chatContainer" @scroll="handleScroll" style="overflow-y: auto">
            <div class="chat-messages h-80 overflow-y-auto border p-2 mb-4 relative">
                <template v-for="(message, index) in messages" :key="message.id">
                    <!-- Date divider BEFORE first message of a day -->
                    <ChatMessageItem :message="message" :currentUserId="currentUserId" />
                    <div v-if="showDateDivider(message, index)" class="date">
                        {{ formatDate(message.created_at) }}
                    </div>
                </template>
            </div>
        </div>
        <ChatInputBox @send="sendMessage" />
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
const conversationId = ref(route.params.userId)
const conversationName = ref('Private Chat')

const messages = ref([])
const offset = ref(0)
const limit = 20
const hasMore = ref(true)
const loading = ref(false)

const echo = createEcho(auth.token)
const chatContainer = ref(null)

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

                // in future we can use it to confirm message sent successfully
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
    await nextTick() // wait until DOM updates
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
