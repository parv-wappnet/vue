<template>
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Your Groups</h1>

    <div v-if="groups.length === 0" class="text-gray-600">No groups found.</div>
    <div v-else class="space-y-4">
      <div v-for="group in groups" :key="group.id" class="p-4 border rounded shadow-sm bg-white">
        <h2 class="text-lg font-semibold">Group ID: {{ group.id }}</h2>
        <p class="text-sm text-gray-700">Description: {{ group.description || 'No description' }}</p>
        <p class="text-sm mt-1">Members: {{ group.members.length }}</p>
        <p class="text-sm">Admins: {{ group.admins.join(', ') }}</p>

        <!-- Group.vue -->
        <button class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
          @click="$router.push({ name: 'ChatWindow', params: { userId: group.id } })">
          View Group
        </button>
      </div>
    </div>

    <hr class="my-6" />

    <h2 class="text-xl font-semibold mb-2">Create New Group</h2>
    <form @submit.prevent="createGroup" class="space-y-4">
      <input v-model="newGroup.description" type="text" placeholder="Group description"
        class="w-full p-2 border rounded" />
      <UserSearch @user-found="handleUserFound" />
      <!-- List searched users -->
      <div v-if="searchedUsers.length > 0" class="mt-4 space-y-2">
        <div v-for="user in searchedUsers" :key="user.id"
          class="p-2 border rounded bg-gray-50 flex justify-between items-center">
          <div>
            <p class="font-medium">{{ user.name }} ({{ user.email }})</p>
          </div>
          <div class="flex gap-2">
            <button @click="acceptUser(user)" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
              Accept
            </button>
            <button @click="rejectUser(user)" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
              Reject
            </button>
          </div>
        </div>
      </div>

      <!-- Accepted user IDs displayed -->
      <div v-if="acceptedMembers.length > 0" class="mt-4">
        <h4 class="text-sm font-semibold">Accepted Member Email:</h4>
        <p class="text-sm text-gray-800">{{acceptedMembers.map(m => m.email).join('\n ')}}</p>
      </div>


      <!-- <label class="block text-sm">Add Member IDs (comma separated)</label> -->
      <!-- <input v-model="newGroup.membersInput" type="text" placeholder="e.g., 2,3,4" class="w-full p-2 border rounded" /> -->
      <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        <!-- @click.prevent="createGroup" -->
        Create Group
      </button>

    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@services/axios'
import { useAuthStore } from '@stores/auth'
import UserSearch from '@follow/UserSearch.vue'

const groups = ref([])
const newGroup = ref({
  description: '',
  membersInput: ''
})
const searchedUsers = ref([])
const acceptedMembers = ref([])

const handleUserFound = (user) => {
  // Avoid duplicates in search list
  if (!searchedUsers.value.some(u => u.id === user.id)) {
    searchedUsers.value.push(user)
  }
}

// Accept user
const acceptUser = (user) => {
  if (!acceptedMembers.value.some(u => u.id === user.id)) {
    acceptedMembers.value.push(user)

    // Append user ID to membersInput string
    const currentIds = newGroup.value.membersInput
      .split(',')
      .map(id => id.trim())
      .filter(id => id !== '')

    if (!currentIds.includes(String(user.id))) {
      currentIds.push(String(user.id))
      newGroup.value.membersInput = currentIds.join(',')
    }
  }

  // Remove from searched list
  searchedUsers.value = searchedUsers.value.filter(u => u.id !== user.id)
}

// Reject user
const rejectUser = (user) => {
  searchedUsers.value = searchedUsers.value.filter(u => u.id !== user.id)
}
// Fetch groups on mount
const fetchGroups = async () => {
  try {
    const res = await axios.get('/groups')
    groups.value = res.data
  } catch (err) {
    console.error('❌ Failed to fetch groups:', err)
  }
}

// Create a new group
const createGroup = async () => {
  const members = newGroup.value.membersInput
    .split(',')
    .map(id => parseInt(id.trim()))
    .filter(id => !isNaN(id))
  console.log('Creating group with members:', members)

  try {
    const res = await axios.post('/groups', {
      description: newGroup.value.description,
      members
    })
    alert('✅ Group created!')
    groups.value.push(res.data.group)
    newGroup.value.description = ''
    newGroup.value.membersInput = ''
  } catch (err) {
    console.error('❌ Failed to create group:', err)
    alert('Failed to create group')
  }
}

// View group details (basic usage for now)
const viewGroup = async (id) => {
  try {
    const res = await axios.get(`/groups/${id}`)
    alert(`Group ID ${id} Details:\n\nDescription: ${res.data.description}\nMembers: ${res.data.members.join(', ')}`)
  } catch (err) {
    console.error('❌ Failed to fetch group details:', err)
    alert('You are not authorized to view this group')
  }
}

onMounted(() => {
  fetchGroups()
})
</script>

<style scoped>
body {
  background-color: #f9fafb;
}
</style>
