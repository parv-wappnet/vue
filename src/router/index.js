import { createRouter, createWebHistory } from 'vue-router'
import Landing from '@pages/Landing.vue'
import Login from '@pages/Login.vue'
import Profile from '@pages/Profile.vue'
import Chat from '@pages/Chat.vue'
import SetProfile from '@pages/SetProfile.vue'
import ChatWindow from '@pages/ChatWindow.vue'
import GoogleCallback from '@pages/GoogleCallback.vue'
import Signup from '@pages/Signup.vue'
import Group from '@pages/Group.vue'
import { authGuard } from './routeGuard'

const routes = [
  { path: '/', name: 'Landing', component: Landing },
  { path: '/login', name: 'Login', component: Login },
  { path: '/set-password', name: 'SetProfile', component: SetProfile },
  { path: '/login/callback', name: 'GoogleCallback', component: GoogleCallback },
  { path: '/signup', name: 'Signup', component: Signup },


  // Protected routes
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    beforeEnter: authGuard,
  },
  {
    path: '/chat',
    name: 'Chat',
    component: Chat,
    beforeEnter: authGuard,
  },
  {
    path: '/group',
    name: 'Group',
    component: Group,
    beforeEnter: authGuard,
  },
  {
    path: '/chat/:conversationId',
    name: 'ChatWindow',
    component: ChatWindow,
    beforeEnter: authGuard,
  },

]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
