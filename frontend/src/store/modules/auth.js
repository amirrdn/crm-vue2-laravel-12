import AuthService from '@/services/auth.service'

const state = {
  token: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')).token : null,
  user: null,
  loading: false,
  error: null
}

const mutations = {
  SET_TOKEN(state, token) {
    state.token = token
  },
  SET_USER(state, user) {
    state.user = user
  },
  SET_LOADING(state, loading) {
    state.loading = loading
  },
  SET_ERROR(state, error) {
    state.error = error
  },
  CLEAR_AUTH(state) {
    state.token = null
    state.user = null
    state.error = null
  }
}

const actions = {
  async login({ commit }, credentials) {
    commit('SET_LOADING', true)
    try {
      const data = await AuthService.login(credentials.email, credentials.password)
      commit('SET_TOKEN', data.token)
      commit('SET_USER', data.user)
      return data
    } catch (error) {
      commit('SET_ERROR', error.response?.data?.message || 'Login failed')
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async register({ commit }, userData) {
    commit('SET_LOADING', true)
    try {
      const data = await AuthService.register(
        userData.name,
        userData.email,
        userData.password,
        userData.password_confirmation
      )
      commit('SET_TOKEN', data.token)
      commit('SET_USER', data.user)
      return data
    } catch (error) {
      commit('SET_ERROR', error.response?.data?.message || 'Registration failed')
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async logout({ commit }) {
    try {
      await AuthService.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      commit('CLEAR_AUTH')
    }
  },

  async getCurrentUser({ commit }) {
    commit('SET_LOADING', true)
    try {
      const user = await AuthService.getCurrentUser()
      commit('SET_USER', user)
      return user
    } catch (error) {
      commit('SET_ERROR', error.response?.data?.message || 'Failed to get user data')
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async refreshToken({ commit }) {
    try {
      const data = await AuthService.refreshToken()
      commit('SET_TOKEN', data.token)
      return data
    } catch (error) {
      commit('CLEAR_AUTH')
      throw error
    }
  }
}

const getters = {
  isAuthenticated: state => !!state.token,
  token: state => state.token,
  user: state => state.user,
  loading: state => state.loading,
  error: state => state.error
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
} 