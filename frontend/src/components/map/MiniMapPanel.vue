<template>
  <div class="mini-map-container"
       @mousedown="onMiniMapDown"
       @mousemove="onMiniMapMove"
       @mouseup="onMiniMapUp"
       @mouseleave="onMiniMapUp">
    <img :src="miniMapDataUrl" class="mini-map-img" draggable="false" />
    <canvas ref="frameCanvas" class="mini-map-frame" width="180" height="180"></canvas>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';

const props = defineProps({
  mapData: Array,
  pan: Object,
  zoom: Number,
  width: Number,
  height: Number,
  hexSize: Number
});
const emit = defineEmits(['center-on-coord']);

const MINI_SIZE = 180;
const miniMapDataUrl = ref('');
const frameCanvas = ref(null);
const isDragging = ref(false);

// --- Generate mini map ---
function getMapBounds(mapData, hexSize) {
  // Get real world boundaries of the map
  let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
  for (const hex of mapData) {
    const { x, y } = getHexCenter(hex.coordinate.q, hex.coordinate.r, hexSize);
    minX = Math.min(minX, x - hexSize);
    maxX = Math.max(maxX, x + hexSize);
    minY = Math.min(minY, y - hexSize);
    maxY = Math.max(maxY, y + hexSize);
  }
  return { minX, maxX, minY, maxY };
}

function getHexCenter(q, r, hexSize) {
  const x = hexSize * (Math.sqrt(3) * q  +  Math.sqrt(3)/2 * r);
  const y = hexSize * (3./2. * r);
  return { x, y };
}

function renderMiniMap() {
  if (!props.mapData || props.mapData.length === 0) return;
  const bounds = getMapBounds(props.mapData, props.hexSize);
  const mapW = bounds.maxX - bounds.minX;
  const mapH = bounds.maxY - bounds.minY;
  // Scale to fit the entire map into MINI_SIZE
  const scale = Math.min(MINI_SIZE / mapW, MINI_SIZE / mapH);
  // Offset to center the map in the mini map
  const offsetX = (MINI_SIZE - mapW * scale) / 2 - bounds.minX * scale;
  const offsetY = (MINI_SIZE - mapH * scale) / 2 - bounds.minY * scale;

  const canvas = document.createElement('canvas');
  canvas.width = MINI_SIZE;
  canvas.height = MINI_SIZE;
  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, MINI_SIZE, MINI_SIZE);
  for (const hex of props.mapData) {
    const { x, y } = getHexCenter(hex.coordinate.q, hex.coordinate.r, props.hexSize);
    drawMiniHex(ctx, x * scale + offsetX, y * scale + offsetY, props.hexSize * scale, getHexColor(hex.biome));
  }
  // Save parameters for frame and click
  lastMiniMapParams.value = { scale, offsetX, offsetY, bounds };
  miniMapDataUrl.value = canvas.toDataURL();
}

const lastMiniMapParams = ref({ scale: 1, offsetX: 0, offsetY: 0, bounds: null });

function drawMiniHex(ctx, cx, cy, size, color) {
  ctx.save();
  ctx.beginPath();
  for (let i = 0; i < 6; i++) {
    const angle = Math.PI / 180 * (60 * i - 30);
    const x = cx + size * Math.cos(angle);
    const y = cy + size * Math.sin(angle);
    if (i === 0) ctx.moveTo(x, y);
    else ctx.lineTo(x, y);
  }
  ctx.closePath();
  ctx.fillStyle = color;
  ctx.globalAlpha = 0.95;
  ctx.fill();
  ctx.restore();
}

const biomeColors = {
  'ocean': '#4fc3f7',
  'water': '#1976d2',
  'grass': '#90a955',
  'forest': '#388e3c',
  'mountain': '#6c757d',
  'desert': '#ffe082',
  'tundra': '#20551b',
  'ice': '#ffffff',
  'default': '#cccccc'
};
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

// --- Draw frame of visible area ---
function drawFrame() {
  const canvas = frameCanvas.value;
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, MINI_SIZE, MINI_SIZE);
  const { scale, offsetX, offsetY } = lastMiniMapParams.value;
  if (!scale) return;
  // 1. Visible area in world coordinates
  const viewLeft = (-props.pan.x - props.width / 2) / props.zoom;
  const viewTop = (-props.pan.y - props.height / 2) / props.zoom;
  const viewRight = viewLeft + props.width / props.zoom;
  const viewBottom = viewTop + props.height / props.zoom;
  // 2. Translate these coordinates to mini map
  const miniLeft = viewLeft * scale + offsetX;
  const miniTop = viewTop * scale + offsetY;
  const miniRight = viewRight * scale + offsetX;
  const miniBottom = viewBottom * scale + offsetY;
  ctx.save();
  ctx.strokeStyle = '#fff';
  ctx.lineWidth = 3;
  ctx.shadowColor = 'rgba(0,0,0,0.7)';
  ctx.shadowBlur = 4;
  ctx.setLineDash([]);
  ctx.strokeRect(
    miniLeft,
    miniTop,
    miniRight - miniLeft,
    miniBottom - miniTop
  );
  ctx.restore();
}

function handleMiniMapClick(event) {
  const rect = event.target.getBoundingClientRect();
  const x = event.clientX - rect.left;
  const y = event.clientY - rect.top;
  const { scale, offsetX, offsetY } = lastMiniMapParams.value;
  // Translate coordinates x, y of mini map to world coordinates of the center
  const worldX = (x - offsetX) / scale;
  const worldY = (y - offsetY) / scale;
  emit('center-on-coord', { worldX, worldY });
}

function onMiniMapDown(event) {
  isDragging.value = true;
  handleMiniMapClick(event);
}

function onMiniMapMove(event) {
  if (isDragging.value) {
    handleMiniMapClick(event);
  }
}

function onMiniMapUp() {
  isDragging.value = false;
}

watch(() => props.mapData, () => {
  renderMiniMap();
  nextTick(drawFrame);
}, { immediate: true });
watch([() => props.pan.x, () => props.pan.y, () => props.zoom, () => props.width, () => props.height], () => {
  nextTick(drawFrame);
});
onMounted(() => {
  renderMiniMap();
  drawFrame();
});
</script>

<style scoped>
.mini-map-container {
  position: absolute;
  bottom: 16px;
  right: 16px;
  width: 180px;
  height: 180px;
  z-index: 20;
  box-shadow: 0 2px 8px rgba(0,0,0,0.13);
  border-radius: 8px;
  background: #fff;
  overflow: hidden;
}
.mini-map-img {
  width: 180px;
  height: 180px;
  display: block;
  pointer-events: none;
  user-select: none;
}
.mini-map-frame {
  position: absolute;
  left: 0;
  top: 0;
  width: 180px;
  height: 180px;
  pointer-events: none;
}
</style> 