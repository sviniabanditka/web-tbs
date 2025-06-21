<script setup>
import { useTestEvents } from '@/composables/useTestEvents';

const { subscribe } = useTestEvents(1);

subscribe('TestEvent', (data) => {
    console.log('Received test event:', data)
});
</script>

<template>
  <div class="flex flex-col items-center justify-center h-screen">
    <h1 class="text-green-500 text-4xl font-bold">Tailwind Works</h1>
    <h3 class="text-gray-500 text-lg">Watch the console & network for Websocket events</h3>
  </div>
</template>

<style scoped>
</style>
