<template>
    <div
        class="hex-map-container"
        @wheel.prevent="handleWheel"
        @mousedown="handleMouseDown"
        @mousemove="handleMouseMove"
        @mouseup="handleMouseUp"
        @mouseleave="handleMouseUp"
    >
        <svg class="hex-map-svg" :viewBox="`0 0 ${width} ${height}`">
            <g :transform="`translate(${pan.x} ${pan.y}) scale(${zoom})`">
                <polygon
                    v-for="hex in mapStore.mapData"
                    :key="`${hex.coordinate.q}-${hex.coordinate.r}`"
                    :points="getHexPoints(hex.coordinate.q, hex.coordinate.r)"
                    :fill="getHexColor(hex.biome)"
                    stroke="black"
                    stroke-width="0.2"
                >
                  <title>{{ getHexTooltip(hex) }}</title>
                </polygon>
            </g>
        </svg>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useMapStore } from '@/stores/map';

const mapStore = useMapStore();

const width = ref(window.innerWidth);
const height = ref(window.innerHeight);
const pan = ref({ x: 0, y: 0 });
const zoom = ref(1);
const isPanning = ref(false);
const lastMousePosition = ref({ x: 0, y: 0 });

const hexSize = 50;
const hexWidth = Math.sqrt(3) * hexSize;
const hexHeight = 2 * hexSize;

// Returns the center coordinates of a hex based on q, r
function getHexCenter(q, r) {
    const x = hexSize * (Math.sqrt(3) * q  +  Math.sqrt(3)/2 * r) + width.value / 2 - hexWidth;
    const y = hexSize * (3./2. * r) + height.value / 2 - hexHeight;
    return { x, y };
}

// Returns the points string for the SVG polygon of a hex
function getHexPoints(q, r) {
    const center = getHexCenter(q, r);
    let points = [];
    for (let i = 0; i < 6; i++) {
        const angle_deg = 60 * i - 30;
        const angle_rad = Math.PI / 180 * angle_deg;
        points.push(
            `${center.x + hexSize * Math.cos(angle_rad)},${center.y + hexSize * Math.sin(angle_rad)}`
        );
    }
    return points.join(' ');
}

const biomeColors = {
    'ocean': '#005f73',
    'water': '#457b9d',
    'grass': '#90a955',
    'forest': '#31572c',
    'mountain': '#6c757d',
    'desert': '#f4a261',
    'tundra': '#e0f2f1',
    'ice': '#ffffff',
    'default': '#cccccc'
};

// Returns the color for a given biome
function getHexColor(biome) {
    if (!biome || typeof biome !== 'string') return biomeColors.default;
    const biomeLower = biome.toLowerCase();
    if (biomeLower.includes('mountain')) return biomeColors.mountain;
    if (biomeLower.includes('forest')) return biomeColors.forest;
    if (biomeLower.includes('grass')) return biomeColors.grass;
    if (biomeLower.includes('desert')) return biomeColors.desert;
    if (biomeLower.includes('tundra')) return biomeColors.tundra;
    if (biomeLower.includes('ocean')) return biomeColors.ocean;
    if (biomeLower.includes('water')) return biomeColors.water;
    return biomeColors.default;
}

function handleMouseDown(event) {
    isPanning.value = true;
    lastMousePosition.value = { x: event.clientX, y: event.clientY };
}

function handleMouseMove(event) {
    if (!isPanning.value) return;
    const dx = event.clientX - lastMousePosition.value.x;
    const dy = event.clientY - lastMousePosition.value.y;
    pan.value.x += dx;
    pan.value.y += dy;
    lastMousePosition.value = { x: event.clientX, y: event.clientY };
}

function handleMouseUp() {
    isPanning.value = false;
}

function handleWheel(event) {
    const scaleAmount = 0.1;
    const scale = event.deltaY > 0 ? 1 - scaleAmount : 1 + scaleAmount;

    // Calculate the world coordinates for zoom centering
    const worldX = (event.clientX - pan.value.x) / zoom.value;
    const worldY = (event.clientY - pan.value.y) / zoom.value;

    pan.value.x = event.clientX - worldX * zoom.value * scale;
    pan.value.y = event.clientY - worldY * zoom.value * scale;
    zoom.value *= scale;
}

function updateDimensions() {
    width.value = window.innerWidth;
    height.value = window.innerHeight;
}

onMounted(() => {
    window.addEventListener('resize', updateDimensions);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateDimensions);
});

// Returns the tooltip string for a hex, including resources
function getHexTooltip(hex) {
    let res = '';
    if (hex.resource === null) {
        res = 'none';
    } else if (typeof hex.resource === 'string') {
        res = hex.resource;
    } else if (Array.isArray(hex.resource) && hex.resource.length === 0) {
        res = 'none';
    } else if (
        hex.resource &&
        typeof hex.resource === 'object' &&
        !Array.isArray(hex.resource) &&
        Object.keys(hex.resource).length > 0
    ) {
        res = Object.entries(hex.resource)
            .map(([key, value]) => `${key}: ${value}`)
            .join(', ');
    } else {
        res = 'none';
    }
    return `q: ${hex.coordinate.q}, r: ${hex.coordinate.r}\nBiome: ${hex.biome}\nResource: ${res}`;
}
</script>

<style scoped>
.hex-map-container {
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    cursor: grab;
    background-color: #f0f0f0;
}
.hex-map-container:active {
    cursor: grabbing;
}
.hex-map-svg {
    width: 100%;
    height: 100%;
}
polygon {
    transition: fill 0.2s ease-in-out;
}
polygon:hover {
    fill: #ffc107;
}
</style> 