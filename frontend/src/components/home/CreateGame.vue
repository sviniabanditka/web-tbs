<template>
  <section>
    <form @submit.prevent="createGame" class="space-y-6 max-w-xl">
      <div>
        <label class="block mb-1 font-semibold">Name</label>
        <input v-model="form.name" type="text" placeholder="Enter name" class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">Seed</label>
        <div class="flex">
            <input v-model="form.seed" type="text" placeholder="Enter seed" class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button type="button" :class="buttonClasses" class="ml-3">Generate</button>
        </div>
      </div>
      <div>
        <label class="block mb-1 font-semibold">Players count</label>
        <input v-model.number="form.playersCount" type="number" min="2" max="8" class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <div class="flex items-center space-x-8">
        <label class="flex items-center space-x-2">
          <input v-model="form.privacy" type="radio" name="privacy" value="public" class="accent-blue-600" />
          <span>Public</span>
        </label>
        <label class="flex items-center space-x-2">
          <input v-model="form.privacy" type="radio" name="privacy" value="private" class="accent-blue-600" />
          <span>Private</span>
        </label>
      </div>
      <button type="submit" :class="buttonClasses">Create</button>
    </form>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const buttonClasses = "bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition";

const form = ref({
  name: '',
  seed: '',
  playersCount: 4,
  privacy: 'public'
})

const emit = defineEmits(['create-game'])

function createGame() {
  emit('create-game', form.value)
  // Here you can add logic to reset the form or handle success
}
</script> 