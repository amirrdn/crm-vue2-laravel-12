import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store'
import Contacts from '../views/Contacts.vue'
import CallLogs from '../views/CallLogs.vue'
import Login from '../views/Login.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    redirect: '/contacts'
  },
  {
    path: '/contacts',
    name: 'Contacts',
    component: Contacts,
    meta: { requiresAuth: true }
  },
  {
    path: '/call-logs',
    name: 'CallLogs',
    component: CallLogs,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach(async (to, from, next) => {
  const isAuthenticated = store.getters['auth/isAuthenticated']
  
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      })
      return
    }
  }
  
  if (to.matched.some(record => record.meta.guest)) {
    if (isAuthenticated) {
      const redirect = to.query.redirect || '/contacts'
      next({ path: redirect })
      return
    }
  }
  
  next()
})

export default router 