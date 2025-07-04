<!-- src/components/ChatWindow.vue -->
<template>
    <div class="chat-container p-4">
        <h3 class="text-lg font-bold mb-4">Conversation: {{ conversationName }}</h3>

        <div class="chat-messages h-80 overflow-y-auto border p-2 mb-4">
            <ChatMessageItem v-for="message in messages" :key="message.id" :message="message"
                :currentUserId="currentUserId" />
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

// Get or create conversation
const initConversation = async () => {
    try {
        const res = await axios.get(`/conversations/private/${userId.value}`)
        conversationName.value = res.data.name || 'Private Chat2'
        const id = res.data.conversation_id
        conversationId.value = id
        console.log(conversationName.value, 'ID:', id)
        await loadMessages()

        echo.private(`chat.${id}`)
            .listen('.MessageSent', (e) => {
                if (e.sender_id !== auth.user.id) {
                    messages.value.unshift(e)
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
</style>
