<template>
  <div class="groups-container">
    <div class="groups-card">
      <h1 class="groups-title">Your Groups</h1>

      <div v-if="groups.length === 0" class="text-center text-lg text-gray-600">
        No groups found.
      </div>
      <div v-else class="space-y-4">
        <div v-for="group in groups" :key="group.id"
          class="group-item p-4 border rounded bg-white shadow-md hover:shadow-lg transition">
          <p class="text-sm text-gray-700">name: {{ group.name || 'No name' }}</p>
          <button class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
            @click="$router.push({ name: 'ChatWindow', params: { conversationId: group.conversations.id } })">
            View Group
          </button>
        </div>
      </div>

      <hr class="divider my-6" />

      <h2 class="create-group-title">Create New Group</h2>
      <form @submit.prevent="createGroup" class="space-y-4">
        <div class="input-group">
          <label for="group-name" class="block text-sm text-gray-600">Group Name</label>
          <div class="input-wrapper">
            <span class="input-icon">📝</span>
            <input v-model="newGroup.name" id="group-name" type="text" placeholder="Group name" required
              class="w-full p-2 border rounded focus:outline-none" />
          </div>
        </div>
        <UserSearch @user-found="handleUserFound" />
        <div v-if="searchedUsers.length > 0" class="mt-4 space-y-2">
          <div v-for="user in searchedUsers" :key="user.id"
            class="user-item p-2 border rounded bg-white flex justify-between items-center hover:bg-gray-100 transition">
            <div>
              <p class="font-medium text-gray-800">{{ user.name }} ({{ user.email }})</p>
            </div>
            <div class="flex gap-2">
              <button @click="acceptUser(user)"
                class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition">
                Accept
              </button>
              <button @click="rejectUser(user)"
                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
                Reject
              </button>
            </div>
          </div>
        </div>

        <div v-if="acceptedMembers.length > 0" class="mt-4">
          <h4 class="text-sm font-semibold text-gray-700">Accepted Member Emails:</h4>
          <p class="text-sm text-gray-800">{{acceptedMembers.map(m => m.email).join(', ')}}</p>
        </div>

        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
          Create Group
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@services/axios'
import { useAuthStore } from '@stores/auth'
import UserSearch from '@follow/UserSearch.vue'

const groups = ref([])
const newGroup = ref({
  name: '',
  membersInput: ''
})
const searchedUsers = ref([])
const acceptedMembers = ref([])

const handleUserFound = (user) => {
  if (!searchedUsers.value.some(u => u.id === user.id)) {
    searchedUsers.value.push(user)
  }
}

const acceptUser = (user) => {
  if (!acceptedMembers.value.some(u => u.id === user.id)) {
    acceptedMembers.value.push(user)
    const currentIds = newGroup.value.membersInput
      .split(',')
      .map(id => id.trim())
      .filter(id => id !== '')
    if (!currentIds.includes(String(user.id))) {
      currentIds.push(String(user.id))
      newGroup.value.membersInput = currentIds.join(',')
    }
  }
  searchedUsers.value = searchedUsers.value.filter(u => u.id !== user.id)
}

const rejectUser = (user) => {
  searchedUsers.value = searchedUsers.value.filter(u => u.id !== user.id)
}

const fetchGroups = async () => {
  try {
    const res = await axios.get('/groups')
    groups.value = res.data
  } catch (err) {
    console.error('❌ Failed to fetch groups:', err)
  }
}

const createGroup = async () => {
  const members = newGroup.value.membersInput
    .split(',')
    .map(id => parseInt(id.trim()))
    .filter(id => !isNaN(id))
  console.log('Creating group with members:', members)

  try {
    const res = await axios.post('/groups', {
      name: newGroup.value.name,
      members
    })
    alert('✅ Group created!')
    groups.value.push(res.data.group)
    newGroup.value.name = ''
    newGroup.value.membersInput = ''
  } catch (err) {
    console.error('❌ Failed to create group:', err)
    alert('Failed to create group')
  }
}

onMounted(() => {
  fetchGroups()
})
</script>

<style scoped>
.groups-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #4facfe, #00f2fe);
  padding: 20px;
}

.groups-card {
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 600px;
  text-align: center;
}

.groups-title {
  font-size: 2rem;
  color: #333;
  margin-bottom: 20px;
}

.create-group-title {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 15px;
  font-weight: 600;
}

.input-group {
  text-align: left;
}

.input-group label {
  display: block;
  font-size: 0.9rem;
  color: #555;
  margin-bottom: 5px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 10px;
  color: #999;
  font-size: 1.2rem;
}

input {
  width: 100%;
  padding: 10px 10px 10px 40px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.3s;
}

input:focus {
  border-color: #4facfe;
}

.group-item,
.user-item {
  border-radius: 5px;
  transition: box-shadow 0.3s;
}

.group-item:hover,
.user-item:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

button {
  padding: 8px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background-color 0.3s;
}

.divider {
  border: 0;
  height: 1px;
  background: #eee;
}

@media (max-width: 480px) {
  .groups-card {
    padding: 20px;
  }

  .groups-title {
    font-size: 1.5rem;
  }

  .create-group-title {
    font-size: 1.2rem;
  }

  input {
    font-size: 0.9rem;
    padding: 8px 8px 8px 35px;
  }

  button {
    font-size: 0.8rem;
    padding: 6px 12px;
  }

  .group-item,
  .user-item {
    padding: 8px;
  }
}
</style>