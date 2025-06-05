import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import axios from '@/utils/axios'
import auth from './modules/auth'
import contacts from './modules/contacts'
import calls from './modules/calls'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    auth,
    contacts,
    calls
  },
  state: {
    loading: false,
    error: null
  },
  mutations: {
    SET_LOADING(state, loading) {
      state.loading = loading
    },
    SET_ERROR(state, error) {
      state.error = error
    }
  },
  actions: {
    async fetchCallLogs({ commit }) {
      commit('SET_LOADING', true)
      try {
        const response = await axios.get('/call-logs')
        return response.data
      } catch (error) {
        commit('SET_ERROR', error.message)
        throw error
      } finally {
        commit('SET_LOADING', false)
      }
    }
  },
  plugins: [
    createPersistedState({
      paths: ['auth.token', 'auth.user'],
      storage: window.localStorage
    })
  ]
}) 