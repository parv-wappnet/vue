<template>
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Chat Page</h1>
    
    <!-- <UserSearch @user-found="selectedUser = $event" /> -->
    <ChatRequest :user="selectedUser" />

    <FollowRequests />
    <AcceptedFollows />
  </div>
</template>

<script setup>
import { ref } from 'vue'
// import UserSearch from '@follow/UserSearch.vue'
import ChatRequest from '@chat/ChatRequest.vue'
import FollowRequests from '@follow/FollowRequests.vue'
import AcceptedFollows from '@follow/AcceptedFollows.vue'

const selectedUser = ref(null)
</script>
