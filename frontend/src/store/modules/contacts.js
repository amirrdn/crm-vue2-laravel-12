import axios from '@/utils/axios'

const state = {
  contacts: [],
  positions: [],
  loading: false,
  error: null
}

const mutations = {
  SET_CONTACTS(state, contacts) {
    state.contacts = contacts
  },
  SET_POSITIONS(state, positions) {
    state.positions = positions
  },
  SET_LOADING(state, loading) {
    state.loading = loading
  },
  SET_ERROR(state, error) {
    state.error = error
  },
  TOGGLE_FAVORITE(state, contactId) {
    const contact = state.contacts.find(c => c.id === contactId)
    if (contact) {
      contact.isFavorite = !contact.isFavorite
    }
  },
  ADD_CONTACT(state, contact) {
    state.contacts.unshift(contact)
  },
  UPDATE_CONTACT(state, updatedContact) {
    const index = state.contacts.findIndex(c => c.id === updatedContact.id)
    if (index !== -1) {
      state.contacts.splice(index, 1, updatedContact)
    }
  },
  DELETE_CONTACT(state, contactId) {
    state.contacts = state.contacts.filter(c => c.id !== contactId)
  }
}

const actions = {
  async fetchContacts({ commit }, params = {}) {
    commit('SET_LOADING', true)
    try {
      const response = await axios.get('/api/contacts', { params })
      commit('SET_CONTACTS', response.data)
    } catch (error) {
      commit('SET_ERROR', 'Failed to fetch contacts.')
      console.error('Error fetching contacts:', error)
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async fetchPositions({ commit }) {
    try {
      console.log('Fetching positions...')
      const response = await axios.get('/api/contacts/positions')
      console.log('Positions response:', response.data)
      commit('SET_POSITIONS', response.data)
    } catch (error) {
      console.error('Error fetching positions:', error)
      commit('SET_ERROR', 'Failed to fetch positions.')
    }
  },

  async toggleFavoriteContact({ commit }, contactId) {
    try {
      const response = await axios.post(`/api/contacts/${contactId}/toggle-favorite`)
      commit('UPDATE_CONTACT', response.data)
      return response.data
    } catch (error) {
      console.error('Error toggling favorite status:', error)
      throw error
    }
  },

  async createContact({ commit }, contactData) {
    try {
      const response = await axios.post('/api/contacts', contactData)
      commit('ADD_CONTACT', response.data)
      return response.data
    } catch (error) {
      console.error('Error creating contact:', error)
      throw error
    }
  },

  async updateContact({ commit }, { id, data }) {
    try {
      const response = await axios.put(`/contacts/${id}`, data)
      commit('UPDATE_CONTACT', response.data)
      return response.data
    } catch (error) {
      commit('SET_ERROR', error.response?.data?.message || 'Failed to update contact')
      throw error
    }
  },

  async deleteContact({ commit }, contactId) {
    try {
      await axios.delete(`/contacts/${contactId}`)
      commit('DELETE_CONTACT', contactId)
    } catch (error) {
      commit('SET_ERROR', error.response?.data?.message || 'Failed to delete contact')
      throw error
    }
  }
}

const getters = {
  getContactById: state => id => {
    return state.contacts.find(contact => contact.id === id)
  },
  favoriteContacts: state => {
    return state.contacts.filter(contact => contact.isFavorite)
  },
  searchContacts: state => query => {
    if (!query) return state.contacts
    const searchQuery = query.toLowerCase()
    return state.contacts.filter(contact => 
      contact.name.toLowerCase().includes(searchQuery) ||
      contact.phone?.toLowerCase().includes(searchQuery) ||
      contact.email?.toLowerCase().includes(searchQuery)
    )
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
} 