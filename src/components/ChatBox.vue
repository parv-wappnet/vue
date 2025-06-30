<template>
  <div>
    <div v-for="(msg, index) in chat.messages" :key="index" class="chat-message">
      <strong>{{ msg.user.name }}:</strong> {{ msg.message }}
    </div>
    <input
      v-model="chat.newMessage"
      @keyup.enter="chat.sendMessage"
      placeholder="Type your message..."
    />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useChatStore } from '../stores/chat'

const chat = useChatStore()

onMounted(() => {
  chat.loadMessages()
  chat.initEcho()
})
</script>

<style scoped>
.chat-message {
  padding: 0.25rem 0;
  border-bottom: 1px solid #eee;
}
input {
  width: 100%;
  padding: 0.5rem;
  margin-top: 1rem;
}
</style>
