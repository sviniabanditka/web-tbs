<template>
    <div class="bg-white/95 rounded-2xl shadow-2xl p-8 flex flex-col items-center">
      <h1 class="text-3xl font-extrabold text-gray-900 mb-2 tracking-tight">Sign In</h1>
      <p v-if="step === 'email'" class="mb-6 text-gray-500 text-sm">Enter your email to receive a login code</p>
      <p v-else class="mb-6 text-gray-500 text-sm">Enter code from email</p>
  
      <form v-if="step === 'email'" @submit.prevent="sendCode" class="w-full flex flex-col gap-4">
        <input
          v-model="email"
          type="email"
          required
          placeholder="user@test.com"
          class="px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-500"
        />
        <button
          type="submit"
          class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg transition shadow-md"
          :disabled="loading"
        >
          {{ loading ? 'Sending...' : 'Get Code' }}
        </button>
      </form>
  
      <form v-else @submit.prevent="login" class="w-full flex flex-col gap-4">
        <input
          v-model="code"
          type="text"
          maxlength="6"
          required
          placeholder="Code from email"
          class="px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-500"
        />
        <button
          type="submit"
          class="bg-green-700 hover:bg-green-800 text-white font-bold py-3 rounded-lg transition shadow-md"
          :disabled="loading"
        >
          {{ loading ? 'Login...' : 'Submit' }}
        </button>
        <button
          type="button"
          class="text-blue-700 hover:underline text-sm mt-2"
          @click="step = 'email'"
        >
          Change email
        </button>
      </form>
  
      <div v-if="error" class="mt-4 text-red-600 font-semibold text-sm">
        {{ error }}
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import axios from 'axios'
  import { useAuthStore } from '@/stores/auth'
  import { useRouter } from 'vue-router'
  
  const email = ref('')
  const code = ref('')
  const step = ref('email')
  const loading = ref(false)
  const error = ref('')
  const authStore = useAuthStore()
  const router = useRouter()
  
  async function sendCode() {
    loading.value = true
    error.value = ''
    try {
      await axios.post('/send-login-code', { email: email.value })
      step.value = 'code'
    } catch (e) {
      error.value = e.response?.data?.message || 'Error while sending login code!'
    }
    loading.value = false
  }
  
  async function login() {
    loading.value = true
    error.value = ''
    try {
      const res = await axios.post('/login-with-code', {
        email: email.value,
        code: code.value
      })
      authStore.setToken(res.data.access_token)
      await authStore.fetchUser()
      router.push({ name: 'home' })
    } catch (e) {
      error.value = e.response?.data?.message || 'Wrong Code!'
    }
    loading.value = false
  }
  </script>
  