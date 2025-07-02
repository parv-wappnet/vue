<!-- ✅ ChatBox.vue -->
<template>
  <div class="chat-box">
    <div v-for="msg in messages" :key="msg.id"
      :class="['chat-message', msg.sender_id === currentUserId ? 'sent' : 'received']">
      <strong>{{ msg.sender.name }}:</strong> {{ msg.body }}
      <span class="text-xs text-gray-500">{{ formatDate(msg.created_at) }}</span>
    </div>

    <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Type your message..."
      class="w-full p-2 border rounded mt-2" />
    <button @click="sendMessage" class="mt-2 p-2 bg-blue-500 text-white rounded w-full">Send</button>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from '../axios'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const conversationId = ref(route.params.conversationId)
const messages = ref([])
const newMessage = ref('')
const authStore = useAuthStore()
const currentUserId = computed(() => authStore.user?.id)

const loadMessages = async () => {
  try {
    const res = await axios.get(`/conversations/${conversationId.value}/messages`)
    messages.value = res.data.reverse()
  } catch (e) {
    console.error('Failed to load messages', e)
  }
}

const sendMessage = async () => {
  if (newMessage.value.trim()) {
    try {
      await axios.post(`/conversations/${conversationId.value}/messages`, {
        body: newMessage.value
      })
      newMessage.value = ''
      await loadMessages()
    } catch (e) {
      console.error('Failed to send message', e)
    }
  }
}

const formatDate = (date) => new Date(date).toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' })

onMounted(loadMessages)
</script>

<style scoped>
.chat-box {
  max-width: 600px;
  margin: 0 auto;
  padding: 1rem;
}

.chat-message {
  padding: 0.5rem;
  margin-bottom: 0.5rem;
  border-radius: 0.5rem;
}

.sent {
  background-color: #e0f7fa;
  text-align: right;
}

.received {
  background-color: #f0f0f0;
}
</style>
