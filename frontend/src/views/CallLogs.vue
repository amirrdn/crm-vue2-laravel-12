<template>
  <div class="bg-gray-100 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Call Logs</h2>
        <div class="flex items-center space-x-4">
          <div class="relative">
            <select
              v-model="selectedContact"
              class="w-48 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="handleFilterChange"
            >
              <option value="">All Contacts</option>
              <option v-for="contact in contacts" :key="contact.id" :value="contact.id">
                {{ contact.name }}
              </option>
            </select>
          </div>
          <div class="relative">
            <input
              type="date"
              v-model="startDate"
              class="w-40 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="handleFilterChange"
            >
          </div>
          <div class="relative">
            <input
              type="date"
              v-model="endDate"
              class="w-40 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="handleFilterChange"
            >
          </div>
          <div class="relative">
            <input
              type="text"
              class="w-64 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              v-model="searchQuery"
              placeholder="Search call logs..."
              @input="debouncedSearch"
            >
          </div>
        </div>
      </div>

      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        {{ error }}
      </div>

      <div v-else-if="filteredCallLogs.length === 0" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4">
        No call logs found
      </div>

      <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
          <li v-for="log in filteredCallLogs" :key="log.id">
            <div class="px-4 py-4 sm:px-6">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <i class="fas fa-phone text-gray-400"></i>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ log.contact?.name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ log.contact?.phone }}
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-4">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': log.status === 'completed',
                      'bg-red-100 text-red-800': log.status === 'failed',
                      'bg-yellow-100 text-yellow-800': log.status === 'missed'
                    }"
                  >
                    {{ log.status }}
                  </span>
                  <span class="text-sm text-gray-500">
                    {{ formatDuration(log.duration) }}
                  </span>
                  <span class="text-sm text-gray-500">
                    {{ formatDate(log.started_at) }}
                  </span>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import moment from 'moment'
import debounce from 'lodash/debounce'
import axios from '@/utils/axios'

export default {
  name: 'CallLogs',
  data() {
    return {
      searchQuery: '',
      selectedContact: '',
      startDate: '',
      endDate: '',
      contacts: [],
      debouncedSearch: null,
      filteredLogs: []
    }
  },
  computed: {
    ...mapState('calls', ['callLogs', 'loading', 'error']),
    filteredCallLogs() {
      let logs = this.callLogs

      // Filter by contact
      if (this.selectedContact) {
        logs = logs.filter(log => log.contact_id === parseInt(this.selectedContact))
      }

      // Filter by date range
      if (this.startDate) {
        logs = logs.filter(log => moment(log.started_at).isSameOrAfter(this.startDate, 'day'))
      }
      if (this.endDate) {
        logs = logs.filter(log => moment(log.started_at).isSameOrBefore(this.endDate, 'day'))
      }

      // Filter by search query
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        logs = logs.filter(log => 
          log.contact?.name?.toLowerCase().includes(query) ||
          log.contact?.phone?.toLowerCase().includes(query) ||
          log.status?.toLowerCase().includes(query)
        )
      }

      return logs
    }
  },
  created() {
    this.fetchCallLogs()
    this.fetchContacts()
    this.debouncedSearch = debounce(this.handleSearch, 300)
  },
  beforeDestroy() {
    if (this.debouncedSearch) {
      this.debouncedSearch.cancel()
    }
  },
  methods: {
    ...mapActions('calls', ['fetchCallLogs']),
    formatDate(date) {
      return moment(date).format('YYYY-MM-DD HH:mm:ss')
    },
    formatDuration(seconds) {
      if (!seconds) return '00:00'
      const minutes = Math.floor(seconds / 60)
      const remainingSeconds = seconds % 60
      return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
    },
    async fetchContacts() {
      try {
        const response = await axios.get('/contacts')
        if (response.data && Array.isArray(response.data)) {
          this.contacts = response.data
        } else {
          console.error('Invalid contacts data format:', response.data)
        }
      } catch (error) {
        console.error('Failed to fetch contacts:', error)
        this.$message.error('Failed to load contact list')
      }
    },
    handleFilterChange() {
      const filters = {}
      if (this.selectedContact) filters.contact_id = this.selectedContact
      if (this.startDate) filters.startDate = this.startDate
      if (this.endDate) filters.endDate = this.endDate
      this.fetchCallLogs(filters)
    },
    handleSearch() {
      // Search is handled in computed property
      this.filteredLogs = this.filteredCallLogs
    }
  }
}
</script>
