<template>
    <div>
      <h1 class="text-2xl font-bold">Game View</h1>
      <button @click="logout" class="btn-secondary mt-4">Выйти</button>
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
  