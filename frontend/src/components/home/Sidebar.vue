<template>
  <aside class="w-72 bg-gray-100 p-6 flex flex-col h-full">
    <div class="flex-grow">
      <div class="flex flex-col items-center mb-6">
        <img :src="user?.avatar || avatarSrc" alt="Avatar" class="w-20 h-20 rounded-full mb-2 border-4 border-blue-500 shadow" />
        <div class="font-semibold text-lg text-gray-800">{{ user?.username }}</div>
      </div>
      <hr class="border-gray-300 mb-6" />
      <nav class="flex flex-col space-y-3">
        <button
          v-for="item in menu"
          :key="item.key"
          class="text-left px-4 py-2 rounded-lg font-medium transition"
          :class="activeTab === item.key ? 'bg-blue-600 text-white shadow' : 'hover:bg-blue-600 hover:text-white text-gray-700'"
          @click="$emit('update:activeTab', item.key)"
        >
          {{ item.label }}
        </button>
      </nav>
    </div>
    <hr class="border-gray-300 mb-3" />
    <button @click="$emit('logout')" class="text-left px-4 py-2 rounded-lg font-medium transition hover:bg-red-600 hover:text-white text-gray-700">
      Logout
    </button>
  </aside>
</template>

<script setup>
import avatarSrc from '@/assets/img/avatar_placeholder.png'

defineProps({
  user: {
    type: Object,
    required: true
  },
  menu: {
    type: Array,
    required: true
  },
  activeTab: {
    type: String,
    required: true
  }
})

defineEmits(['update:activeTab', 'logout'])
</script> 