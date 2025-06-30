<template>
  <div>
    <h1>Chat Page</h1>
    <UserSearch />
    <FollowRequests />
    <AcceptedFollows />
    <router-link to="/profile">← Back to Profile</router-link>
  </div>
</template>

<script setup>
import UserSearch from '../components/UserSearch.vue'
import FollowRequests from '../components/FollowRequests.vue'
import AcceptedFollows from '../components/AcceptedFollows.vue'
</script>
