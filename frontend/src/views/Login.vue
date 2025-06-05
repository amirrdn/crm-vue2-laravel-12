<template>
  <div class="flex items-center justify-center from-indigo-100 via-white to-purple-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl">
      <div class="flex justify-center">
        <div class="h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center transform hover:scale-110 transition-transform duration-200">
          <span class="text-2xl font-bold text-white">CRM</span>
        </div>
      </div>

      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          {{ isLogin ? 'Welcome Back!' : 'Create Account' }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          {{ isLogin ? 'Sign in to access your dashboard' : 'Join us to get started' }}
        </p>
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="isLogin ? handleLogin() : register()">
        <div class="space-y-4">
          <div v-if="!isLogin" class="transform transition-all duration-300">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-200"
              placeholder="Enter your name"
            />
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-200"
              placeholder="Enter your email"
            />
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-200"
              placeholder="Enter your password"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-[1.02] transition-all duration-200 shadow-lg"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
            </span>
            {{ isLogin ? 'Sign in' : 'Create Account' }}
          </button>
        </div>

        <!-- <div class="text-center">
          <button
            type="button"
            class="text-indigo-600 hover:text-indigo-500 font-medium transition-colors duration-200"
            @click="isLogin = !isLogin"
          >
            {{ isLogin ? 'Need an account? Register' : 'Already have an account? Sign in' }}
          </button>
        </div> -->
      </form>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import AuthService from '../services/auth.service'

export default {
  name: 'Login',
  data() {
    return {
      isLogin: true,
      form: {
        name: '',
        email: '',
        password: ''
      }
    }
  },
  methods: {
    ...mapActions('auth', ['login']),
    async handleLogin() {
      try {
        await this.$store.dispatch('auth/login', {
          email: this.form.email,
          password: this.form.password
        })
        const redirect = this.$route.query.redirect || '/contacts'
        this.$router.replace(redirect).catch(() => {})
      } catch (error) {
        this.$message.error('Login failed')
      }
    },
    async register() {
      try {
        const data = await AuthService.register({
          name: this.form.name,
          email: this.form.email,
          password: this.form.password
        })
        if (data && data.token) {
          await this.login({ token: data.token, user: data.user })
          await this.$nextTick()
          await this.$router.replace('/')
        } else {
          alert('Registration failed. Please try again.')
        }
      } catch (error) {
        console.error('Registration failed:', error)
        alert('Registration failed. Please try again.')
      }
    }
  }
}
</script>