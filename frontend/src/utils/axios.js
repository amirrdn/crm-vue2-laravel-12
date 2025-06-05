import axios from 'axios'
import store from '@/store'

const api = axios.create({
  baseURL: process.env.VUE_APP_API_URL || 'http://localhost:8000',
  timeout: 5000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Request-Type': 'api'
  },
  withCredentials: true
})

api.interceptors.request.use(
  config => {
    const token = store.state.auth.token
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    if (config.url.startsWith('/') && !config.url.startsWith('/api/')) {
      config.url = `/api${config.url}`
    }

    config.headers['X-Request-ID'] = Math.random().toString(36).substring(7)
    config.headers['X-Request-Timestamp'] = new Date().toISOString()

    return config
  },
  error => {
    return Promise.reject(error)
  }
)

api.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      store.dispatch('auth/refreshToken')
    }
    return Promise.reject(error)
  }
)

export default api 