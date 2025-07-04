<template>
    <div :class="['message-wrapper', isSent ? 'sent' : 'received']">
        <div class="message-bubble">
            <p class="sender-email">{{ message.sender.email }}</p>
            <p class="message-text">{{ message.content }}</p>
            <span class="timestamp">{{ formatDate(message.created_at) }}</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    message: Object,
    currentUserId: Number,
})

const isSent = computed(() => props.message.sender_id === props.currentUserId)

const formatDate = (date) =>
    new Date(date).toLocaleString('en-IN', {
        timeZone: 'Asia/Kolkata',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    })
</script>

<style scoped>
.message-wrapper {
    display: flex;
    margin-bottom: 0.5rem;
    padding: 0 0.5rem;
}

.sent {
    justify-content: flex-end;
}

.received {
    justify-content: flex-start;
}

.message-bubble {
    background-color: #e0f7fa;
    /* sent (blue) */
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    max-width: 60%;
    word-break: break-word;
    position: relative;
}

.received .message-bubble {
    background-color: #f1f1f1;
    /* received (gray) */
}

.sender-email {
    font-size: 0.8rem;
    color: #666;
    margin: 0 0 0.25rem 0;
    font-weight: 500;
}

.timestamp {
    display: block;
    font-size: 0.75rem;
    color: #555;
    margin-top: 0.25rem;
    text-align: right;
}
</style>
