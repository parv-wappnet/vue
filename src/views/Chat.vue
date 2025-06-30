<template>
  <div>
    <h1>Chat Page</h1>
    <UserSearch />
    <FollowRequests />
    <router-link to="/profile">← Back to Profile</router-link>
  </div>
</template>

<script setup>
import UserSearch from '../components/UserSearch.vue'
import FollowRequests from '../components/FollowRequests.vue'
</script>
