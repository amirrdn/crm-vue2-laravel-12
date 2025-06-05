import axios from '@/utils/axios'

const state = {
  callLogs: [],
  loading: false,
  error: null
}

const mutations = {
  SET_CALL_LOGS(state, callLogs) {
    state.callLogs = callLogs
  },
  SET_LOADING(state, loading) {
    state.loading = loading
  },
  SET_ERROR(state, error) {
    state.error = error
  },
  ADD_CALL_LOG(state, callLog) {
    state.callLogs.unshift(callLog)
  }
}

const actions = {
  async fetchCallLogs({ commit }, filters = {}) {
    commit('SET_LOADING', true)
    try {
      const params = new URLSearchParams()
      if (filters.user_id) params.append('user_id', filters.user_id)
      if (filters.contact_id) params.append('contact_id', filters.contact_id)
      if (filters.startDate) params.append('startDate', filters.startDate)
      if (filters.endDate) params.append('endDate', filters.endDate)
      
      const response = await axios.get(`/call-logs?${params.toString()}`)
      commit('SET_CALL_LOGS', response.data)
    } catch (error) {
      commit('SET_ERROR', error.message)
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async createCallLog({ commit }, callLog) {
    try {
      const response = await axios.post('/call-logs', callLog)
      commit('ADD_CALL_LOG', response.data)
      return response.data
    } catch (error) {
      commit('SET_ERROR', error.message)
      throw error
    }
  }
}

const getters = {
  getCallLogs: state => state.callLogs,
  isLoading: state => state.loading,
  getError: state => state.error
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
} 