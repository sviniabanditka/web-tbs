<template>
  <section>
    <div class="overflow-y-auto h-full p-2">
      <ul class="space-y-4">
        <li v-for="game in games" :key="game.id" class="p-5 border border-gray-300 rounded-lg hover:bg-gray-100 transition flex justify-between items-center">
          <div>
            <div class="font-semibold text-lg">{{ game.name }}</div>
            <div class="text-sm text-gray-600">Status: {{ game.status }}</div>
          </div>
          <button :class="buttonClasses" @click="$emit('open-game', game.id)">
            Open
          </button>
        </li>
      </ul>
    </div>
  </section>
</template>

<script setup>
defineProps({
  games: {
    type: Array,
    required: true
  }
})

defineEmits(['open-game'])

const buttonClasses = "bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition";
</script> 