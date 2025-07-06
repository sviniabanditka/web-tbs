<template>
  <div class="controls">
    <div class="tabs">
      <button
        v-for="tab in tabs"
        :key="tab"
        :class="['tab-btn', { active: currentTab === tab }]"
        @click="currentTab = tab"
      >
        {{ tab }}
      </button>
    </div>
    <div v-if="currentTab === 'General'">
      <div class="flex items-center gap-2 mb-2">
        <label for="seed" class="w-28 whitespace-nowrap text-gray-700 font-medium">
          Seed:
        </label>
        <input
          id="seed"
          type="text"
          v-model="localSeed"
          class="flex-1 px-2 py-1 border rounded"
        />
      </div>
      <div v-if="seedError" class="input-error">{{ seedError }}</div>
      <div class="flex items-center gap-2 mb-2">
        <label for="radius" class="w-28 whitespace-nowrap text-gray-700 font-medium">
          Radius:
        </label>
        <input
          id="radius"
          type="number"
          v-model.number="localRadius"
          min="10"
          max="50"
          step="1"
          class="flex-1 px-2 py-1 border rounded text-right"
        />
      </div>
      <div v-if="radiusError" class="input-error">{{ radiusError }}</div>
      <div class="flex items-center gap-2 mb-2">
        <label for="biome-size" class="w-28 whitespace-nowrap text-gray-700 font-medium">
          Biome size:
        </label>
        <input
          id="biome-size"
          type="number"
          v-model.number="localBiomeSize"
          min="0.1"
          max="1.0"
          step="0.01"
          class="flex-1 px-2 py-1 border rounded text-right"
        />
      </div>
      <div v-if="biomeSizeError" class="input-error">{{ biomeSizeError }}</div>
      <div class="flex items-center gap-2 mb-2">
        <label for="abundance" class="w-28 whitespace-nowrap text-gray-700 font-medium">
          Resources count:
        </label>
        <input
          id="abundance"
          type="number"
          v-model.number="localAbundance"
          min="0.1"
          max="1.0"
          step="0.01"
          class="flex-1 px-2 py-1 border rounded text-right"
        />
      </div>
      <div v-if="abundanceError" class="input-error">{{ abundanceError }}</div>
    </div>
    <div v-else-if="currentTab === 'Biomes'">
      <div v-for="biome in biomesList" :key="biome" class="flex items-center gap-2 mb-2">
        <label :for="'biome-' + biome" class="w-28 whitespace-nowrap text-gray-700 font-medium">
          {{ capitalize(biome) }}:
        </label>
        <input
          :id="'biome-' + biome"
          type="number"
          v-model.number="localBiomes[biome]"
          min="0.1"
          max="1.0"
          step="0.01"
          class="flex-1 px-2 py-1 border rounded text-right"
        />
        <span v-if="biomesErrors[biome]" class="input-error">{{ biomesErrors[biome] }}</span>
      </div>
    </div>
    <div v-else-if="currentTab === 'Resources'">
      <div v-for="resource in resources" :key="resource" class="flex items-center gap-2 mb-2">
        <label :for="'resource-' + resource" class="w-28 whitespace-nowrap text-gray-700 font-medium">
          {{ capitalize(resource) }}:
        </label>
        <input
          :id="'resource-' + resource"
          type="number"
          v-model.number="localScarcity[resource]"
          min="0.1"
          max="1.0"
          step="0.01"
          class="flex-1 px-2 py-1 border rounded text-right"
        />
        <span v-if="scarcityErrors[resource]" class="input-error">{{ scarcityErrors[resource] }}</span>
      </div>
    </div>
    <div class="flex">
      <button class="control-btn main-btn" @click="$emit('random-seed')" :disabled="hasErrors">Random Seed</button>
      <button class="control-btn main-btn" @click="$emit('load-map')" :disabled="mapStore.loading || hasErrors">
        {{ mapStore.loading ? 'Loading...' : 'Load Map' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, toRefs, watchEffect, computed } from 'vue';
import { capitalize } from 'vue';

const props = defineProps({
  seed: String,
  radius: Number,
  biomeSize: Number,
  abundance: Number,
  scarcity: Object,
  biomes: Object,
  resources: Array,
  biomesList: Array,
  mapStore: Object
});
const emit = defineEmits([
  'update:seed',
  'update:radius',
  'update:biomeSize',
  'update:abundance',
  'update:scarcity',
  'update:biomes',
  'random-seed',
  'load-map'
]);

const tabs = ['General', 'Biomes', 'Resources'];
const currentTab = ref('General');

const localSeed = ref(props.seed);
const localRadius = ref(props.radius);
const localBiomeSize = ref(props.biomeSize);
const localAbundance = ref(props.abundance);
const localScarcity = ref({ ...props.scarcity });
const localBiomes = ref({ ...props.biomes });

watch(() => props.seed, (newSeed) => {
  if (newSeed !== localSeed.value) {
    localSeed.value = newSeed;
  }
});

watch(() => props.biomes, (newBiomes) => {
  if (JSON.stringify(newBiomes) !== JSON.stringify(localBiomes.value)) {
    localBiomes.value = { ...newBiomes };
  }
});

watch(() => props.scarcity, (newScarcity) => {
  if (JSON.stringify(newScarcity) !== JSON.stringify(localScarcity.value)) {
    localScarcity.value = { ...newScarcity };
  }
});

watch(localSeed, v => emit('update:seed', v));
watch(localRadius, v => emit('update:radius', v));
watch(localBiomeSize, v => emit('update:biomeSize', v));
watch(localAbundance, v => emit('update:abundance', v));
watch(localScarcity, v => emit('update:scarcity', { ...v }), { deep: true });
watch(localBiomes, v => emit('update:biomes', { ...v }), { deep: true });

// Валидация
const seedError = computed(() => {
  if (!localSeed.value || localSeed.value.length < 3) return 'Seed must be at least 3 characters';
  if (localSeed.value.length > 20) return 'Seed must be at most 20 characters';
  return '';
});
const radiusError = computed(() => {
  if (typeof localRadius.value !== 'number' || isNaN(localRadius.value)) return 'Radius is required';
  if (localRadius.value < 10) return 'Radius must be at least 10';
  if (localRadius.value > 50) return 'Radius must be at most 50';
  return '';
});
const biomeSizeError = computed(() => {
  if (typeof localBiomeSize.value !== 'number' || isNaN(localBiomeSize.value)) return 'Biome size is required';
  if (localBiomeSize.value < 0.1) return 'Min: 0.1';
  if (localBiomeSize.value > 1.0) return 'Max: 1.0';
  return '';
});
const abundanceError = computed(() => {
  if (typeof localAbundance.value !== 'number' || isNaN(localAbundance.value)) return 'Resource count is required';
  if (localAbundance.value < 0.1) return 'Min: 0.1';
  if (localAbundance.value > 1.0) return 'Max: 1.0';
  return '';
});
const scarcityErrors = computed(() => {
  const errs = {};
  for (const key in localScarcity.value) {
    const v = localScarcity.value[key];
    if (typeof v !== 'number' || isNaN(v)) errs[key] = 'Required';
    else if (v < 0.1) errs[key] = 'Min: 0.1';
    else if (v > 1.0) errs[key] = 'Max: 1.0';
    else errs[key] = '';
  }
  return errs;
});
const biomesErrors = computed(() => {
  const errs = {};
  for (const key in localBiomes.value) {
    const v = localBiomes.value[key];
    if (typeof v !== 'number' || isNaN(v)) errs[key] = 'Required';
    else if (v < 0.1) errs[key] = 'Min: 0.1';
    else if (v > 1.0) errs[key] = 'Max: 1.0';
    else errs[key] = '';
  }
  return errs;
});
const hasErrors = computed(() => {
  if (seedError.value || radiusError.value || biomeSizeError.value || abundanceError.value) return true;
  if (Object.values(scarcityErrors.value).some(e => e)) return true;
  if (Object.values(biomesErrors.value).some(e => e)) return true;
  return false;
});
</script>

<style scoped>
.controls {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 10;
    background: rgba(255, 255, 255, 0.95);
    padding: 18px 18px 14px 18px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
    min-width: 320px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.tabs {
    display: flex;
    flex-direction: row;
    gap: 8px;
    margin-bottom: 10px;
}
.tab-btn {
    background: #e3eaf6;
    color: #1976d2;
    border: none;
    border-radius: 5px 5px 0 0;
    padding: 7px 18px 6px 18px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.18s, color 0.18s;
}
.tab-btn.active {
    background: #1976d2;
    color: #fff;
}
.control-row {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
}
label {
    flex: 1;
    font-weight: 500;
    color: #222;
}
input[type="text"], input[type="number"] {
    width: 100%;
    padding: 5px 8px;
    border: 1px solid #bbb;
    border-radius: 4px;
    font-size: 15px;
    margin-top: 2px;
}
.control-btn {
    background: #1976d2;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 8px 16px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(25, 118, 210, 0.08);
    transition: background 0.18s, box-shadow 0.18s;
    margin-left: 4px;
}
.control-btn:hover:not(:disabled) {
    background: #1565c0;
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.13);
}
.control-btn:disabled {
    background: #b0b0b0;
    cursor: not-allowed;
}
.main-btn {
    margin-top: 8px;
    width: 100%;
    font-size: 16px;
    padding: 10px 0;
    letter-spacing: 0.5px;
}
.input-error {
  color: #d32f2f;
  font-size: 13px;
  margin-left: 8px;
  margin-bottom: 2px;
}
</style> 