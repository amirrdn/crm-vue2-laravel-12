<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <nav-header />
    <nav-bar />

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <router-view/>
    </main>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import NavHeader from '@/components/NavHeader.vue'
import NavBar from '@/components/NavBar.vue'

export default {
  name: 'App',
  components: {
    NavHeader,
    NavBar
  },
  computed: {
    ...mapGetters('auth', ['isAuthenticated', 'user'])
  }
}
</script>