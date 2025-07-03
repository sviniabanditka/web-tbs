<template>
    <div>
        <div class="controls">
            <div class="control-row">
                <label>
                    Seed:
                    <input type="text" v-model="seed" />
                </label>
                <button class="control-btn" @click="randomSeed">Random</button>
            </div>
            <div class="control-row">
                <label>
                    Radius:
                    <input type="number" v-model.number="radius" min="10" max="100" step="5" />
                </label>
            </div>
            <div class="control-row">
                <label>
                    Biome size:
                    <input type="number" v-model.number="biomeSize" min="0.1" max="1.0" step="0.1" />
                </label>
            </div>
            <button class="control-btn main-btn" @click="loadMap" :disabled="mapStore.loading">
                {{ mapStore.loading ? 'Loading...' : 'Load Map' }}
            </button>
        </div>
        <div v-if="mapStore.error" class="error-message">
            {{ mapStore.error }}
        </div>
        <MapInfoPanel v-if="mapStore.stats" />
        <HexMap v-if="mapStore.mapData && mapStore.mapData.length > 0" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import HexMap from '@/components/HexMap.vue';
import MapInfoPanel from '@/components/MapInfoPanel.vue';
import { useMapStore } from '@/stores/map';

const mapStore = useMapStore();
const seed = ref(randomString());
const radius = ref(25);
const biomeSize = ref(0.5);

function randomString(length = 12) {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

function randomSeed() {
    seed.value = randomString();
    loadMap();
}

function loadMap() {
    mapStore.fetchMap(seed.value, radius.value, biomeSize.value);
}
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
    min-width: 220px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
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
.error-message {
    position: absolute;
    top: 60px;
    left: 10px;
    z-index: 10;
    background: rgba(255, 100, 100, 0.8);
    color: white;
    padding: 10px;
    border-radius: 5px;
}
</style> 