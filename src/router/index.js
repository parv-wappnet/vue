import { createRouter, createWebHistory } from 'vue-router'
import Landing from '../views/Landing.vue'
import Login from '../views/Login.vue'
import Profile from '../views/Profile.vue'
import Chat from '../views/Chat.vue'
import SetPassword from '../views/SetPassword.vue'

const routes = [
  { path: '/', name: 'Landing', component: Landing },
  { path: '/login', name: 'Login', component: Login },
  { path: '/set-password', name: 'SetPassword', component: SetPassword },
  { path: '/profile', name: 'Profile', component: Profile },
  { path: '/chat', name: 'Chat', component: Chat },
  {
  path: '/login/callback',
  name: 'GoogleCallback',
  component: () => import('../views/GoogleCallback.vue')
},
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
