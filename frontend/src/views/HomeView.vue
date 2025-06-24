<template>
  <div class="mx-auto w-full px-6 min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 flex items-center justify-center p-6">
    <div class="bg-white/95 rounded-3xl shadow-2xl w-full max-w-7xl h-[90vh] flex overflow-hidden">
      <Sidebar
        :user="user"
        :menu="menu"
        :active-tab="activeTab"
        @update:active-tab="activeTab = $event"
        @logout="logout"
      />

      <main class="flex-1 p-10 overflow-auto bg-white/80">
        <component
          :is="activeComponent"
          :key="activeTab"
          v-bind="activeComponentProps"
          @create-game="handleCreateGame"
          @join-game="joinGame"
          @open-game="openGame"
          @save-settings="handleSaveSettings"
        />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

import Sidebar from '@/components/home/Sidebar.vue'
import CreateGame from '@/components/home/CreateGame.vue'
import BrowseGames from '@/components/home/BrowseGames.vue'
import MyGames from '@/components/home/MyGames.vue'
import Settings from '@/components/home/Settings.vue'

const authStore = useAuthStore()
const router = useRouter()

const { user } = storeToRefs(authStore)

const menu = [
  { key: 'create', label: 'Create Game' },
  { key: 'search', label: 'Browse Games' },
  { key: 'games', label: 'My Games' },
  { key: 'settings', label: 'Settings' }
]

const activeTab = ref('search')

const publicGames = ref([
  { id: 1, name: 'Public Game 1', playersCount: 2, maxPlayers: 4 },
  { id: 2, name: 'Public Game 2', playersCount: 1, maxPlayers: 6 },
  { id: 3, name: 'Public Game 3', playersCount: 2, maxPlayers: 4 },
  { id: 4, name: 'Public Game 4', playersCount: 1, maxPlayers: 6 },
  { id: 5, name: 'Public Game 5', playersCount: 2, maxPlayers: 4 },
  { id: 6, name: 'Public Game 6', playersCount: 1, maxPlayers: 6 },
  { id: 7, name: 'Public Game 7', playersCount: 2, maxPlayers: 4 },
  { id: 8, name: 'Public Game 8', playersCount: 1, maxPlayers: 6 },
  { id: 9, name: 'Public Game 9', playersCount: 2, maxPlayers: 4 },
  { id: 10, name: 'Public Game 10', playersCount: 1, maxPlayers: 6 },
])

const myGames = ref([
  { id: 101, name: 'My Game 1', status: 'active' },
  { id: 102, name: 'My Game 2', status: 'finished' }
])

const componentMap = {
  create: CreateGame,
  search: BrowseGames,
  games: MyGames,
  settings: Settings
}

const activeComponent = computed(() => componentMap[activeTab.value])

const activeComponentProps = computed(() => {
  switch (activeTab.value) {
    case 'search':
      return { games: publicGames.value }
    case 'games':
      return { games: myGames.value }
    case 'settings':
      return { user: user.value }
    default:
      return {}
  }
})

function joinGame(id) {
  alert(`Joining to the game with ID: ${id}`)
  // Example navigation: router.push({ name: 'game', params: { id } })
}

function openGame(id) {
  alert(`Opening game with ID: ${id}`)
  // Example navigation: router.push({ name: 'game', params: { id } })
}

function handleCreateGame(formData) {
  console.log('Creating game with data:', formData)
  alert('Creating game with data: ' + JSON.stringify(formData))
  // Here you would typically make an API call to create a game
  // e.g. axios.post('/games', formData).then(res => {
  //   myGames.value.push(res.data)
  //   activeTab.value = 'games'
  // })
}

function handleSaveSettings(formData) {
  console.log('Saving settings with data:', formData)
  alert('Saving settings with data: ' + JSON.stringify(formData))
  user.value = { ...user.value, ...formData }
  // Here you would typically make an API call to save user settings
}

function logout() {
  authStore.clearAuth()
  router.push({ name: 'login' })
}
</script>
  