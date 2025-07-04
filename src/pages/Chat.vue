<template>
  <div>
    <h1>Chat Page</h1>
    <UserSearch />
    <FollowRequests />
    <AcceptedFollows />
    <!-- <router-link to="/profile">← Back to Profile</router-link> -->
  </div>
</template>

<script setup>
import UserSearch from '@follow/UserSearch.vue'
import FollowRequests from '@follow/FollowRequests.vue'
import AcceptedFollows from '@follow/AcceptedFollows.vue'
</script>
