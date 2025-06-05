<template>
  <div class="bg-gray-100 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Contacts</h2>
        <div class="flex items-center space-x-4">
          <div class="relative">
            <input
              type="text"
              class="w-64 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              v-model="searchQuery"
              placeholder="Search contacts..."
              @input="searchContacts"
            >
          </div>
          <div class="relative">
            <div class="relative">
              <select
                v-model="selectedPosition"
                class="w-48 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white"
                @change="filterByPosition"
              >
                <option value="">All Positions</option>
                <option v-for="position in positions" :key="position" :value="position">
                  {{ position }}
                </option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-briefcase text-gray-400"></i>
              </div>
              <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
              </div>
            </div>
          </div>
          <button 
            @click="showFavoritesOnly = !showFavoritesOnly"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium"
            :class="showFavoritesOnly ? 'bg-yellow-500 text-white hover:bg-yellow-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
          >
            <i class="fas fa-star mr-2"></i>
            {{ showFavoritesOnly ? 'All Contacts' : 'Favorites' }}
          </button>
          <button 
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            @click="showAddModal = true"
          >
            <i class="fas fa-plus mr-2"></i>Add Contact
          </button>
        </div>
      </div>

      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        {{ error }}
      </div>

      <div v-else-if="filteredContacts.length === 0" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4">
        No contacts found
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="contact in filteredContacts" :key="contact.id" class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-900">{{ contact.name }}</h3>
                <p v-if="contact.position || contact.company" class="text-sm text-gray-500">{{ contact.position }}{{ (contact.position && contact.company) ? ' at ' : '' }}{{ contact.company }}</p>
              </div>
              <button
                @click="toggleFavorite(contact)"
                class="relative z-10 p-3 -mt-1 -mr-1 rounded-full hover:bg-gray-100 hover:border hover:border-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200 cursor-pointer"
                :title="contact.is_favorite ? 'Remove from favorites' : 'Add to favorites'"
              >
                <i class="fas text-2xl" :class="contact.is_favorite ? 'fa-star text-yellow-500' : 'fa-star-o text-gray-400 hover:text-yellow-400'"></i>
              </button>
            </div>
            <div class="mt-4 space-y-2">
              <p class="text-sm text-gray-600">
                <i class="fas fa-phone mr-2"></i>{{ contact.phone }}
              </p>
              <p v-if="contact.email" class="text-sm text-gray-600">
                <i class="fas fa-envelope mr-2"></i>{{ contact.email }}
              </p>
              <p v-if="contact.notes" class="text-sm text-gray-600">
                <i class="fas fa-sticky-note mr-2"></i>{{ contact.notes }}
              </p>
            </div>
            <div class="mt-6 flex justify-between space-x-2">
              <button
                class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                @click="callContact(contact)"
                :disabled="!contact.phone"
              >
                <i class="fas fa-phone mr-2"></i>Call
              </button>
              <button
                class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white"
                :class="contact.is_favorite ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-500 hover:bg-gray-600'"
                @click="toggleFavorite(contact)"
              >
                <i class="fas fa-star mr-2"></i>{{ contact.is_favorite ? 'Unfavorite' : 'Favorite' }}
              </button>
              <button
                class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click="viewCallHistory(contact.id)"
              >
                <i class="fas fa-history mr-2"></i>History
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showAddModal" class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
          </div>

          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add New Contact</h3>
                  <form @submit.prevent="handleAddContact" class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Name</label>
                      <input
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        v-model="newContact.name"
                        required
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Phone</label>
                      <input
                        type="tel"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        v-model="newContact.phone"
                        required
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Email</label>
                      <input
                        type="email"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        v-model="newContact.email"
                        required
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Company</label>
                      <input
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        v-model="newContact.company"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Position</label>
                      <input
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        v-model="newContact.position"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Notes</label>
                      <textarea
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        v-model="newContact.notes"
                        rows="3"
                      ></textarea>
                    </div>
                    <div class="flex items-center">
                      <input
                        type="checkbox"
                        id="is_favorite"
                        v-model="newContact.is_favorite"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      >
                      <label for="is_favorite" class="ml-2 block text-sm text-gray-700">
                        Mark as favorite
                      </label>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                @click="handleAddContact"
                :disabled="isSubmitting"
              >
                <span v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"></span>
                Add Contact
              </button>
              <button
                type="button"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                @click="showAddModal = false"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showCallPopup" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
          </div>

          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Calling {{ currentCall?.contact?.name }}
                  </h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">
                      {{ currentCall?.contact?.phone }}
                    </p>
                    <p class="mt-2 text-sm text-gray-500">
                      Status: {{ currentCall?.status }}
                    </p>
                    
                    <!-- Enhanced Duration Display -->
                    <div class="mt-4 text-center">
                      <p class="text-sm font-medium text-gray-500">Duration</p>
                      <p class="text-3xl font-bold text-indigo-600 mt-1">
                        <i class="fas fa-clock mr-2"></i> {{ formattedDuration }}
                      </p>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                @click="endCall"
              >
                End Call
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'Contacts',
  data() {
    return {
      searchQuery: '',
      selectedPosition: '',
      showFavoritesOnly: false,
      showAddModal: false,
      showCallPopup: false,
      currentCall: null,
      isSubmitting: false,
      newContact: {
        name: '',
        phone: '',
        email: '',
        company: '',
        position: '',
        notes: '',
        is_favorite: false
      },
      elapsedDuration: 0,
      durationTimer: null,
      debounceTimer: null
    }
  },
  computed: {
    ...mapState('contacts', ['contacts', 'positions', 'loading', 'error']),
    uniquePositions() {
      return this.positions
    },
    filteredContacts() {
      return this.contacts
    },
    formattedDuration() {
      const minutes = Math.floor(this.elapsedDuration / 60);
      const seconds = this.elapsedDuration % 60;
      const formattedMinutes = String(minutes).padStart(2, '0');
      const formattedSeconds = String(seconds).padStart(2, '0');
      return `${formattedMinutes}:${formattedSeconds}`;
    }
  },
  methods: {
    ...mapActions('contacts', ['fetchContacts', 'createContact', 'toggleFavoriteContact', 'fetchPositions']),
    ...mapActions('calls', ['createCallLog']),
    
    async fetchFilteredContacts() {
      console.log('Fetching filtered contacts...')
      const params = {}
      
      if (this.searchQuery) {
        params.search = this.searchQuery
      }
      
      if (this.selectedPosition) {
        params.position = this.selectedPosition
      }
      
      if (this.showFavoritesOnly) {
        params.favorites_only = '1'
      }
      
      await this.fetchContacts(params)
    },

    searchContacts() {
      if (this.debounceTimer) {
        clearTimeout(this.debounceTimer)
      }
      this.debounceTimer = setTimeout(() => {
        this.fetchFilteredContacts()
      }, 300)
    },
    
    async filterByPosition() {
      this.searchQuery = ''
      await this.fetchFilteredContacts()
    },

    async toggleFavorite(contact) {
      try {
        await this.toggleFavoriteContact(contact.id)
        await this.fetchFilteredContacts()
      } catch (error) {
        console.error('Failed to toggle favorite:', error)
        if (this.$toast) {
          this.$toast.error('Failed to update favorite status')
        } else {
          alert('Failed to update favorite status')
        }
      }
    },
    
    async callContact(contact) {
      try {
        this.currentCall = {
          contact: contact,
          status: 'calling',
          duration: 0,
          startTime: new Date()
        }
        this.elapsedDuration = 0;
        this.showCallPopup = true

        this.durationTimer = setInterval(() => {
          this.elapsedDuration++;
        }, 1000);

      } catch (error) {
        console.error('Failed to handle call:', error)
        this.showCallPopup = false
        this.currentCall = null
        if (this.durationTimer) {
            clearInterval(this.durationTimer);
            this.durationTimer = null;
        }
      }
    },
    
    async endCall() {
      if (this.durationTimer) {
        clearInterval(this.durationTimer);
        this.durationTimer = null;
      }

      try {
        if (this.currentCall) {
          await this.createCallLog({
            contact_id: this.currentCall.contact.id,
            started_at: this.currentCall.startTime.toISOString(),
            duration: this.elapsedDuration,
            status: 'completed'
          })
        }
      } catch (error) {
        console.error('Failed to end call:', error)
        if (this.$toast) {
          this.$toast.error('Failed to save call log');
        } else {
          alert('Failed to save call log');
        }
      } finally {
        this.showCallPopup = false
        this.currentCall = null
        this.elapsedDuration = 0;
      }
    },
    
    viewCallHistory(contactId) {
      this.$router.push({
        name: 'CallLogs',
        query: { contact_id: contactId }
      })
    },

    async handleAddContact() {
      this.isSubmitting = true
      try {
        await this.createContact(this.newContact)
        this.showAddModal = false
        this.resetNewContact()
      } catch (error) {
        console.error('Failed to add contact:', error)
        if (this.$toast) {
          this.$toast.error('Failed to add contact');
        } else {
          alert('Failed to add contact');
        }
      } finally {
        this.isSubmitting = false
      }
    },

    resetNewContact() {
      this.newContact = {
        name: '',
        phone: '',
        email: '',
        company: '',
        position: '',
        notes: '',
        is_favorite: false
      }
    },

  },
  watch: {
    showFavoritesOnly: {
      handler: 'fetchFilteredContacts',
      immediate: false
    }
  },
  async created() {
    try {
      await this.$store.dispatch('contacts/fetchPositions')
      await this.fetchFilteredContacts()
    } catch (error) {
      console.error('Error initializing contacts:', error)
    }
  },
  beforeDestroy() {
    if (this.durationTimer) {
      clearInterval(this.durationTimer);
    }
  }
}
</script>