<template>
  <section>
    <form @submit.prevent="saveSettings" class="space-y-6 max-w-xl">
      <div>
        <label class="block mb-1 font-semibold">Avatar URL</label>
        <input v-model="form.avatar" type="text" placeholder="Avatar URL" class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">Username</label>
        <input v-model="form.username" type="text" placeholder="Enter Username" class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">Email</label>
        <input v-model="form.email" type="email" placeholder="Enter email" class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <button type="submit" :class="buttonClasses">Save</button>
    </form>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const buttonClasses = "bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition";

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['save-settings'])

const form = ref({
  avatar: '',
  username: '',
  email: ''
})

onMounted(() => {
  if (props.user) {
    form.value.avatar = props.user.avatar || ''
    form.value.username = props.user.username || ''
    form.value.email = props.user.email || ''
  }
})

function saveSettings() {
  emit('save-settings', form.value)
}
</script> 