<template>
    <div>
        <MapControlsPanel
            v-model:seed="seed"
            v-model:radius="radius"
            v-model:biomeSize="biomeSize"
            v-model:abundance="abundance"
            v-model:scarcity="scarcity"
            v-model:biomes="biomes"
            :resources="resources"
            :biomes-list="biomesList"
            :map-store="mapStore"
            @random-seed="randomSeed"
            @load-map="loadMap"
        />
        <div v-if="mapStore.error" class="error-message">
            {{ mapStore.error }}
        </div>
        <MapInfoPanel v-if="mapStore.stats" />
        <HexMap v-if="mapStore.mapData && mapStore.mapData.length > 0"
                v-model:pan="pan"
                v-model:zoom="zoom"
                :width="width"
                :height="height"
                :hex-size="hexSize"
        />
        <MiniMap
            v-if="mapStore.mapData && mapStore.mapData.length > 0"
            :map-data="mapStore.mapData"
            :pan="pan"
            :zoom="zoom"
            :width="width"
            :height="height"
            :hex-size="hexSize"
            @center-on-coord="handleMiniMapCenter"
        />
    </div>
</template>

<script setup>
import { ref, shallowRef } from 'vue';
import HexMap from '@/components/HexMap.vue';
import MapInfoPanel from '@/components/MapInfoPanel.vue';
import MapControlsPanel from '@/components/MapControlsPanel.vue';
import { useMapStore } from '@/stores/map';
import { capitalize } from 'vue';
import MiniMap from '@/components/MiniMap.vue';

const mapStore = useMapStore();
const seed = ref(randomString());
const radius = ref(25);
const biomeSize = ref(0.5);
const abundance = ref(0.8);
const resources = ['food', 'wood', 'stone', 'iron', 'gold'];
const scarcity = ref({
    food: 0.8,
    wood: 0.8,
    stone: 0.7,
    iron: 0.5,
    gold: 0.3,
});
const biomesList = ['grass', 'forest', 'mountain', 'tundra', 'desert', 'water'];
const biomes = ref({
    grass: 0.3,
    forest: 0.15,
    mountain: 0.15,
    tundra: 0.1,
    desert: 0.1,
    water: 0.2,
});

// Для управления pan/zoom из MapView
const pan = ref({ x: 0, y: 0 });
const zoom = ref(1);
const width = ref(window.innerWidth);
const height = ref(window.innerHeight);
const hexSize = ref(50); // тот же, что в HexMap

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
    mapStore.fetchMap(
        seed.value,
        radius.value,
        biomeSize.value,
        abundance.value,
        { ...scarcity.value },
        { ...biomes.value }
    );
}

// Функция для центрирования карты по клику на миникарте
function handleMiniMapCenter({ worldX, worldY }) {
    // worldX, worldY — мировые координаты точки, куда кликнули на миникарте
    // Нужно выставить pan так, чтобы эта точка оказалась в центре экрана
    const viewWidth = width.value / zoom.value;
    const viewHeight = height.value / zoom.value;
    pan.value = {
        x: -(worldX + viewWidth / 2) * zoom.value + width.value / 2,
        y: -(worldY + viewHeight / 2) * zoom.value + height.value / 2
    };
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