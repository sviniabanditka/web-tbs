<template>
    <div
        class="hex-map-container"
        @wheel.prevent="handleWheel"
        @mousedown="handleMouseDown"
        @mousemove="handleMouseMove"
        @mouseup="handleMouseUp"
        @mouseleave="handleMouseUp"
    >
        <canvas ref="canvasRef"
                :width="width"
                :height="height"
                class="hex-map-canvas"></canvas>
        <div v-if="hoveredHex"
             class="hex-tooltip"
             :style="tooltipStyle">
            <div>q: {{ hoveredHex.coordinate.q }}, r: {{ hoveredHex.coordinate.r }}</div>
            <div>Biome: {{ hoveredHex.biome }}</div>
            <div>Resource: {{ getHexTooltipResource(hoveredHex) }}</div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { useMapStore } from '@/stores/map';

const mapStore = useMapStore();

const props = defineProps({
  pan: { type: Object, required: true },
  zoom: { type: Number, required: true },
  width: { type: Number, required: true },
  height: { type: Number, required: true },
  hexSize: { type: Number, default: 50 }
});

const emit = defineEmits(['update:pan', 'update:zoom']);

const isPanning = ref(false);
const lastMousePosition = ref({ x: 0, y: 0 });
const hoveredHex = ref(null);
const mousePos = ref({ x: 0, y: 0 });
const canvasRef = ref(null);

const hexSize = computed(() => props.hexSize);
const hexWidth = computed(() => Math.sqrt(3) * hexSize.value);
const hexHeight = computed(() => 2 * hexSize.value);

const biomeColors = {
    'ocean': '#4fc3f7',      // light blue for ocean
    'water': '#1976d2',      // more saturated blue
    'grass': '#90a955',      // keep
    'forest': '#388e3c',     // saturated green, to not blend with grass
    'mountain': '#6c757d',   // keep
    'desert': '#ffe082',     // bright yellow, more natural
    'tundra': '#20551b',     // slightly darker than before
    'ice': '#ffffff',        // keep
    'default': '#cccccc'
};

const iconMap = {
    food: '🍎',
    wood: '🌲',
    stone: '🪨',
    iron: '⛓️',
    gold: '🪙',
};

const devicePixelRatio = window.devicePixelRatio || 1;

// Returns the center coordinates of a hex based on q, r
function getHexCenter(q, r) {
    const x = hexSize.value * (Math.sqrt(3) * q  +  Math.sqrt(3)/2 * r);
    const y = hexSize.value * (3./2. * r);
    return { x, y };
}

// Returns the points string for the SVG polygon of a hex
function getHexPoints(q, r) {
    const center = getHexCenter(q, r);
    let points = [];
    for (let i = 0; i < 6; i++) {
        const angle_deg = 60 * i - 30;
        const angle_rad = Math.PI / 180 * angle_deg;
        points.push({
            x: center.x + hexSize.value * Math.cos(angle_rad),
            y: center.y + hexSize.value * Math.sin(angle_rad)
        });
    }
    return points;
}

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

function getHexTooltipResource(hex) {
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
    return res;
}

function setCanvasSize() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    // Set internal size with consideration of devicePixelRatio
    canvas.width = props.width * devicePixelRatio;
    canvas.height = props.height * devicePixelRatio;
    canvas.style.width = props.width + 'px';
    canvas.style.height = props.height + 'px';
}

function render() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    setCanvasSize();
    const ctx = canvas.getContext('2d');
    ctx.setTransform(1, 0, 0, 1, 0, 0); // reset
    ctx.clearRect(0, 0, props.width, props.height);
    ctx.save();
    ctx.scale(devicePixelRatio, devicePixelRatio);
    ctx.translate(props.pan.x + props.width / 2, props.pan.y + props.height / 2);
    ctx.scale(props.zoom, props.zoom);

    // --- Viewport culling ---
    // 1. Calculate world boundaries of visible area
    const viewLeft = (-props.pan.x - props.width / 2) / props.zoom;
    const viewTop = (-props.pan.y - props.height / 2) / props.zoom;
    const viewRight = viewLeft + props.width / props.zoom;
    const viewBottom = viewTop + props.height / props.zoom;

    // 2. For each hex check if it's in viewport
    for (const hex of mapStore.mapData) {
        const center = getHexCenter(hex.coordinate.q, hex.coordinate.r);
        // Bounding box of hex
        const minX = center.x - hexSize.value;
        const maxX = center.x + hexSize.value;
        const minY = center.y - hexSize.value;
        const maxY = center.y + hexSize.value;
        if (
            maxX < viewLeft || minX > viewRight ||
            maxY < viewTop || minY > viewBottom
        ) {
            continue; // hex outside screen
        }
        const isHovered = hoveredHex.value &&
            hex.coordinate.q === hoveredHex.value.coordinate.q &&
            hex.coordinate.r === hoveredHex.value.coordinate.r;
        drawHex(ctx, hex.coordinate.q, hex.coordinate.r, hex.biome, hex.resource, isHovered);
    }
    ctx.restore();
}

function drawHex(ctx, q, r, biome, resource, isHovered) {
    const points = getHexPoints(q, r);
    ctx.save();
    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);
    for (let i = 1; i < points.length; i++) {
        ctx.lineTo(points[i].x, points[i].y);
    }
    ctx.closePath();
    ctx.fillStyle = getHexColor(biome);
    ctx.globalAlpha = isHovered ? 0.7 : 1.0;
    ctx.fill();
    ctx.globalAlpha = 1.0;
    ctx.strokeStyle = 'black';
    ctx.lineWidth = Math.max(0.8 / props.zoom, 0.5);
    ctx.stroke();
    ctx.restore();

    // Draw resource icons
    if (props.zoom > 0.7) {
        const icons = getResourceIcons(resource);
        if (icons.length > 0) {
            ctx.save();
            // Font size now depends on zoom (decreases with distance)
            const fontSize = Math.max(18 * props.zoom, 10); // 18 base, but decreases with zoom < 1
            ctx.font = `${fontSize}px sans-serif`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            const center = getHexCenter(q, r);
            for (let i = 0; i < icons.length; i++) {
                ctx.fillText(
                    icons[i],
                    center.x,
                    center.y + i * fontSize - (icons.length - 1) * fontSize / 2
                );
            }
            ctx.restore();
        }
    }
}

function getHexAtCanvasPos(x, y) {
    // Convert mouse coordinates to world coordinates
    const worldX = (x - props.pan.x - props.width / 2) / props.zoom;
    const worldY = (y - props.pan.y - props.height / 2) / props.zoom;
    // Reverse formula for axial coordinates
    const q = (Math.sqrt(3)/3 * worldX - 1/3 * worldY) / hexSize.value;
    const r = (2/3 * worldY) / hexSize.value;
    // Round to nearest hex
    return hexRound(q, r);
}

function hexRound(q, r) {
    // axial -> cube
    let x = q;
    let z = r;
    let y = -x - z;
    let rx = Math.round(x);
    let ry = Math.round(y);
    let rz = Math.round(z);
    const x_diff = Math.abs(rx - x);
    const y_diff = Math.abs(ry - y);
    const z_diff = Math.abs(rz - z);
    if (x_diff > y_diff && x_diff > z_diff) {
        rx = -ry - rz;
    } else if (y_diff > z_diff) {
        ry = -rx - rz;
    } else {
        rz = -rx - ry;
    }
    return { q: rx, r: rz };
}

function findHex(q, r) {
    return mapStore.mapData.find(hex => hex.coordinate.q === q && hex.coordinate.r === r);
}

function handleMouseDown(event) {
    isPanning.value = true;
    lastMousePosition.value = { x: event.clientX, y: event.clientY };
}

function handleMouseMove(event) {
    mousePos.value = { x: event.clientX, y: event.clientY };
    if (isPanning.value) {
        const dx = event.clientX - lastMousePosition.value.x;
        const dy = event.clientY - lastMousePosition.value.y;
        const newPan = { x: props.pan.x + dx, y: props.pan.y + dy };
        emit('update:pan', newPan);
        lastMousePosition.value = { x: event.clientX, y: event.clientY };
        render();
        return;
    }
    // Hover logic
    const { q, r } = getHexAtCanvasPos(event.clientX, event.clientY);
    const hex = findHex(q, r);
    if (hex) {
        if (!hoveredHex.value || hoveredHex.value.coordinate.q !== q || hoveredHex.value.coordinate.r !== r) {
            hoveredHex.value = hex;
            render();
        }
    } else {
        if (hoveredHex.value) {
            hoveredHex.value = null;
            render();
        }
    }
}

function handleMouseUp() {
    isPanning.value = false;
}

function handleWheel(event) {
    const scaleAmount = 0.1;
    const scale = event.deltaY > 0 ? 1 - scaleAmount : 1 + scaleAmount;
    // Center zoom relative to mouse
    const rect = canvasRef.value.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const mouseY = event.clientY - rect.top;
    const worldX = (mouseX - props.pan.x - props.width / 2) / props.zoom;
    const worldY = (mouseY - props.pan.y - props.height / 2) / props.zoom;
    let newZoom = props.zoom * scale;
    newZoom = Math.max(newZoom, 0.4); // limit minimum zoom
    // Correct pan so that zoom is relative to cursor
    const newPan = {
        x: mouseX - worldX * newZoom - props.width / 2,
        y: mouseY - worldY * newZoom - props.height / 2
    };
    emit('update:pan', newPan);
    emit('update:zoom', newZoom);
    render();
}

function updateDimensions() {
    render();
}

onMounted(() => {
    window.addEventListener('resize', updateDimensions);
    render();
});

onUnmounted(() => {
    window.removeEventListener('resize', updateDimensions);
});

watch([() => mapStore.mapData, () => props.width, () => props.height, () => props.pan.x, () => props.pan.y, () => props.zoom], () => {
    render();
});

// Tooltip positioning
const tooltipStyle = computed(() => {
    if (!hoveredHex.value) return {};
    return {
        position: 'fixed',
        left: mousePos.value.x + 16 + 'px',
        top: mousePos.value.y + 16 + 'px',
        background: 'rgba(255,255,255,0.95)',
        border: '1px solid #888',
        padding: '6px 10px',
        borderRadius: '6px',
        pointerEvents: 'none',
        zIndex: 10,
        fontSize: '14px',
        boxShadow: '0 2px 8px rgba(0,0,0,0.08)'
    };
});

// Returns an array of resource icons for a hex resource
function getResourceIcons(resource) {
    if (!resource) return [];
    if (typeof resource === 'string') {
        return [iconMap[resource] || '❓'];
    }
    if (typeof resource === 'object' && !Array.isArray(resource)) {
        return Object.keys(resource).map(key => iconMap[key] || '❓');
    }
    return [];
}
</script>

<style scoped>
.hex-map-container {
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    position: relative;
    background-color: #f0f0f0;
    user-select: none;
}
.hex-map-canvas {
    width: 100vw;
    height: 100vh;
    display: block;
    cursor: grab;
}
.hex-map-container:active .hex-map-canvas {
    cursor: grabbing;
}
.hex-tooltip {
    pointer-events: none;
    min-width: 120px;
}
</style> 