<template>
    <div>
      <h1 class="text-2xl font-bold">Main Menu</h1>
      <!-- <router-link to="/game/new" class="btn">Create New Game</router-link> -->
      <ul>
        <li v-for="game in games" :key="game?.id">
          <router-link :to="`/game/${game?.id}`">{{ game?.name }}</router-link>
        </li>
      </ul>
      <button @click="logout" class="btn-secondary mt-4">Logout</button>
    </div>
  </template>
  
  <script setup>
  import { useAuthStore } from '@/stores/auth'
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import { useRouter } from 'vue-router'
  
  const authStore = useAuthStore()
  const games = ref([])
  const router = useRouter()
  
  onMounted(async () => {
    // Get user games
    const res = await axios.get('/games')
    games.value = res.data
  })
  
  function logout() {
    authStore.clearAuth()
    router.push({ name: 'login' })
  }
  </script>
  