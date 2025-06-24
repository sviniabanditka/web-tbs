# Section 12: Graphical Interface and User Experience

## 12.1 UI/UX Design Philosophy

The user interface serves as the bridge between complex strategic mechanics and intuitive player interaction. The design prioritizes **clarity**, **accessibility**, and **efficiency** while maintaining visual appeal and modern web standards.

### Core Design Principles

#### Clarity First
- **Information Hierarchy**: Most important information is prominently displayed
- **Visual Consistency**: Consistent color schemes, typography, and iconography
- **Contextual Information**: Relevant data appears when and where needed
- **Progressive Disclosure**: Complex features revealed gradually as players advance

#### Accessibility and Inclusion
- **Responsive Design**: Seamless experience across desktop, tablet, and mobile
- **Colorblind Support**: Alternative visual indicators beyond color coding
- **Keyboard Navigation**: Full keyboard accessibility for all functions
- **Screen Reader Support**: Proper ARIA labels and semantic HTML

#### Efficiency and Performance
- **Minimal Clicks**: Common actions require minimal user input
- **Predictive Interface**: Anticipate user needs and provide shortcuts
- **Fast Loading**: Optimized assets and progressive loading
- **Smooth Animations**: 60fps interactions with appropriate feedback

## 12.2 Main Interface Layout

### Primary Layout Structure

```vue
<template>
  <div class="game-container">
    <!-- Top Navigation Bar -->
    <nav class="top-navbar">
      <ResourcePanel />
      <TurnInfo />
      <GameMenu />
    </nav>
    
    <!-- Main Game Area -->
    <main class="game-main">
      <!-- Left Sidebar -->
      <aside class="left-sidebar">
        <ActionPanel />
        <UnitList />
        <BuildingQueue />
      </aside>
      
      <!-- Central Game Board -->
      <section class="game-board">
        <HexagonalMap />
        <UIOverlays />
      </section>
      
      <!-- Right Panel -->
      <aside class="right-panel">
        <MinimapView />
        <DiplomacyPanel />
        <TechTree />
      </aside>
    </main>
    
    <!-- Bottom Status Bar -->
    <footer class="status-bar">
      <ChatPanel />
      <NotificationCenter />
      <ConnectionStatus />
    </footer>
  </div>
</template>
```

### Responsive Breakpoints

```css
/* Desktop (1200px+) */
.game-container {
  display: grid;
  grid-template-rows: 60px 1fr 40px;
  grid-template-columns: 300px 1fr 280px;
  height: 100vh;
}

/* Tablet (768px - 1199px) */
@media (max-width: 1199px) {
  .game-container {
    grid-template-columns: 240px 1fr 240px;
  }
  
  .left-sidebar, .right-panel {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .sidebar-open .left-sidebar {
    transform: translateX(0);
  }
}

/* Mobile (767px and below) */
@media (max-width: 767px) {
  .game-container {
    grid-template-columns: 1fr;
    grid-template-rows: 50px 1fr 80px;
  }
  
  .sidebars {
    position: fixed;
    top: 50px;
    left: 0;
    right: 0;
    bottom: 80px;
    background: rgba(0, 0, 0, 0.9);
    z-index: 1000;
  }
}
```

## 12.3 Core Interface Components

### Resource Panel

The resource panel provides constant visibility of player resources and income.

```vue
<template>
  <div class="resource-panel">
    <div v-for="resource in resources" :key="resource.type" class="resource-item">
      <icon :name="resource.icon" class="resource-icon" />
      <div class="resource-info">
        <span class="resource-amount">{{ formatNumber(resource.amount) }}</span>
        <span class="resource-income" :class="resource.incomeClass">
          {{ formatIncome(resource.income) }}
        </span>
      </div>
    </div>
    
    <div class="action-points">
      <div class="ap-display">
        <span class="ap-current">{{ currentAP }}</span>
        <span class="ap-total">/ {{ maxAP }}</span>
      </div>
      <div class="ap-bar">
        <div class="ap-fill" :style="{ width: `${apPercentage}%` }"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useGameStore } from '@/stores/gameStore'

const gameStore = useGameStore()

const resources = computed(() => [
  {
    type: 'gold',
    icon: 'coins',
    amount: gameStore.player.gold,
    income: gameStore.player.goldIncome,
    incomeClass: gameStore.player.goldIncome >= 0 ? 'positive' : 'negative'
  },
  {
    type: 'production',
    icon: 'hammer',
    amount: gameStore.player.production,
    income: gameStore.player.productionIncome,
    incomeClass: 'neutral'
  },
  {
    type: 'science',
    icon: 'flask',
    amount: gameStore.player.science,
    income: gameStore.player.scienceIncome,
    incomeClass: 'neutral'
  },
  {
    type: 'influence',
    icon: 'crown',
    amount: gameStore.player.influence,
    income: gameStore.player.influenceIncome,
    incomeClass: 'neutral'
  }
])

const currentAP = computed(() => gameStore.player.actionPoints)
const maxAP = computed(() => gameStore.player.maxActionPoints)
const apPercentage = computed(() => (currentAP.value / maxAP.value) * 100)
</script>
```

### Hexagonal Game Board

The central game board renders the hexagonal map with all game elements.

```vue
<template>
  <div class="hex-board" ref="boardContainer">
    <!-- SVG Canvas for hex grid -->
    <svg class="hex-grid" :width="boardWidth" :height="boardHeight">
      <!-- Hex tiles -->
      <g v-for="hex in visibleHexes" :key="hex.id">
        <HexTile 
          :hex="hex"
          :selected="selectedHex?.id === hex.id"
          :highlighted="highlightedHexes.includes(hex.id)"
          @click="selectHex"
          @hover="hoverHex"
        />
      </g>
      
      <!-- Units -->
      <g class="units-layer">
        <Unit
          v-for="unit in visibleUnits"
          :key="unit.id"
          :unit="unit"
          :selected="selectedUnit?.id === unit.id"
          @click="selectUnit"
        />
      </g>
      
      <!-- Buildings -->
      <g class="buildings-layer">
        <Building
          v-for="building in visibleBuildings"
          :key="building.id"
          :building="building"
          @click="selectBuilding"
        />
      </g>
      
      <!-- UI Overlays -->
      <g class="ui-overlays">
        <MovementPath v-if="showMovementPath" :path="movementPath" />
        <AttackRange v-if="showAttackRange" :range="attackRange" />
        <VisionOverlay v-if="showVision" :vision="playerVision" />
      </g>
    </svg>
    
    <!-- HTML overlays for complex UI -->
    <div class="html-overlays">
      <Tooltip v-if="tooltip.show" :tooltip="tooltip" />
      <ContextMenu v-if="contextMenu.show" :menu="contextMenu" />
      <CombatPreview v-if="combatPreview.show" :preview="combatPreview" />
    </div>
  </div>
</template>
```

### Action Panel

The action panel provides quick access to all available player actions.

```vue
<template>
  <div class="action-panel">
    <div class="action-categories">
      <button 
        v-for="category in actionCategories"
        :key="category.id"
        class="category-tab"
        :class="{ active: activeCategory === category.id }"
        @click="activeCategory = category.id"
      >
        <icon :name="category.icon" />
        <span>{{ category.name }}</span>
      </button>
    </div>
    
    <div class="action-list">
      <div 
        v-for="action in availableActions"
        :key="action.id"
        class="action-item"
        :class="{ disabled: !action.available, highlighted: action.recommended }"
        @click="executeAction(action)"
      >
        <div class="action-icon">
          <icon :name="action.icon" />
          <span v-if="action.cost" class="action-cost">{{ action.cost }} AP</span>
        </div>
        
        <div class="action-details">
          <h4 class="action-name">{{ action.name }}</h4>
          <p class="action-description">{{ action.description }}</p>
          
          <div v-if="action.requirements" class="action-requirements">
            <span 
              v-for="req in action.requirements"
              :key="req.id"
              class="requirement"
              :class="{ met: req.satisfied }"
            >
              {{ req.name }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
```

## 12.4 Advanced UI Features

### Context-Sensitive Interface

The interface adapts based on player selection and game state:

```javascript
class ContextualUI {
    updateInterface(selection, gameState) {
        const context = this.determineContext(selection, gameState);
        
        switch (context.type) {
            case 'UNIT_SELECTED':
                this.showUnitActions(context.unit);
                this.highlightMovementOptions(context.unit);
                this.updateActionPanel('UNIT_ACTIONS');
                break;
                
            case 'CITY_SELECTED':
                this.showCityManagement(context.city);
                this.updateProductionQueue(context.city);
                this.updateActionPanel('CITY_ACTIONS');
                break;
                
            case 'EMPTY_HEX':
                this.showTerrainInfo(context.hex);
                this.updateActionPanel('GENERAL_ACTIONS');
                break;
                
            case 'ENEMY_UNIT':
                this.showCombatPreview(gameState.selectedUnit, context.unit);
                this.highlightAttackOptions();
                break;
        }
    }
    
    showUnitActions(unit) {
        const actions = [
            { id: 'move', name: 'Move', icon: 'arrow-right', cost: 1 },
            { id: 'attack', name: 'Attack', icon: 'sword', cost: 2 },
            { id: 'fortify', name: 'Fortify', icon: 'shield', cost: 1 },
            { id: 'sleep', name: 'Sleep', icon: 'moon', cost: 0 }
        ];
        
        this.actionPanel.setActions(actions.filter(action => 
            unit.canPerformAction(action.id)
        ));
    }
}
```

### Real-Time Feedback System

```vue
<template>
  <div class="feedback-system">
    <!-- Floating Damage Numbers -->
    <transition-group name="damage-float" tag="div" class="damage-numbers">
      <div
        v-for="damage in activeDamageNumbers"
        :key="damage.id"
        class="damage-number"
        :class="damage.type"
        :style="damage.style"
      >
        {{ damage.value }}
      </div>
    </transition-group>
    
    <!-- Resource Change Indicators -->
    <transition-group name="resource-change" tag="div" class="resource-changes">
      <div
        v-for="change in resourceChanges"
        :key="change.id"
        class="resource-change"
        :class="change.type"
      >
        <icon :name="change.resource" />
        <span>{{ change.amount > 0 ? '+' : '' }}{{ change.amount }}</span>
      </div>
    </transition-group>
    
    <!-- Action Confirmations -->
    <div v-if="pendingAction" class="action-confirmation">
      <div class="confirmation-content">
        <h3>Confirm Action</h3>
        <p>{{ pendingAction.description }}</p>
        <div class="confirmation-buttons">
          <button @click="confirmAction" class="confirm-btn">Confirm</button>
          <button @click="cancelAction" class="cancel-btn">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>
```

### Minimap Component

```vue
<template>
  <div class="minimap-container">
    <div class="minimap-header">
      <h3>World Map</h3>
      <div class="minimap-controls">
        <button @click="centerOnPlayer" class="center-btn">
          <icon name="target" />
        </button>
        <button @click="togglePolitical" class="political-btn" :class="{ active: showPolitical }">
          <icon name="flag" />
        </button>
      </div>
    </div>
    
    <div class="minimap-view" ref="minimapView">
      <canvas 
        ref="minimapCanvas"
        class="minimap-canvas"
        :width="minimapWidth"
        :height="minimapHeight"
        @click="handleMinimapClick"
      ></canvas>
      
      <!-- Viewport indicator -->
      <div 
        class="viewport-indicator"
        :style="viewportStyle"
      ></div>
      
      <!-- Player indicators -->
      <div
        v-for="player in otherPlayers"
        :key="player.id"
        class="player-indicator"
        :style="getPlayerIndicatorStyle(player)"
        :class="{ ally: isAlly(player), enemy: isEnemy(player) }"
      >
        <div class="player-color" :style="{ backgroundColor: player.color }"></div>
      </div>
    </div>
  </div>
</template>
```

## 12.5 Mobile Interface Adaptations

### Touch-Optimized Controls

```javascript
class TouchInterface {
    constructor() {
        this.touchStartTime = 0;
        this.touchStartPos = { x: 0, y: 0 };
        this.longPressThreshold = 500; // milliseconds
        this.swipeThreshold = 50; // pixels
    }
    
    handleTouchStart(event) {
        this.touchStartTime = Date.now();
        this.touchStartPos = {
            x: event.touches[0].clientX,
            y: event.touches[0].clientY
        };
        
        // Start long press timer
        this.longPressTimer = setTimeout(() => {
            this.handleLongPress(event);
        }, this.longPressThreshold);
    }
    
    handleTouchEnd(event) {
        clearTimeout(this.longPressTimer);
        
        const touchDuration = Date.now() - this.touchStartTime;
        const deltaX = event.changedTouches[0].clientX - this.touchStartPos.x;
        const deltaY = event.changedTouches[0].clientY - this.touchStartPos.y;
        const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
        
        if (touchDuration < 200 && distance < 10) {
            // Quick tap
            this.handleTap(event);
        } else if (distance > this.swipeThreshold) {
            // Swipe gesture
            this.handleSwipe(deltaX, deltaY);
        }
    }
    
    handleLongPress(event) {
        // Show context menu
        this.showContextMenu(event.touches[0].clientX, event.touches[0].clientY);
    }
}
```

### Mobile-Specific Components

```vue
<!-- Mobile Action Wheel -->
<template>
  <div v-if="showActionWheel" class="action-wheel" :style="wheelPosition">
    <div
      v-for="(action, index) in wheelActions"
      :key="action.id"
      class="wheel-action"
      :style="getActionPosition(index)"
      @touchend="executeAction(action)"
    >
      <icon :name="action.icon" />
      <span>{{ action.name }}</span>
    </div>
  </div>
</template>

<!-- Mobile Panel Slider -->
<template>
  <div class="mobile-panels">
    <div 
      class="panel-tab"
      v-for="panel in panels"
      :key="panel.id"
      @click="togglePanel(panel.id)"
    >
      <icon :name="panel.icon" />
    </div>
    
    <transition name="slide-up">
      <div v-if="activePanel" class="active-panel">
        <component :is="activePanel.component" />
      </div>
    </transition>
  </div>
</template>
```

## 12.6 Accessibility Features

### Keyboard Navigation

```javascript
class KeyboardNavigation {
    constructor() {
        this.currentFocus = null;
        this.focusableElements = [];
        this.shortcuts = new Map([
            ['Space', 'endTurn'],
            ['Enter', 'confirmAction'],
            ['Escape', 'cancel'],
            ['ArrowUp', 'navigateUp'],
            ['ArrowDown', 'navigateDown'],
            ['ArrowLeft', 'navigateLeft'],
            ['ArrowRight', 'navigateRight'],
            ['Tab', 'nextElement'],
            ['Shift+Tab', 'previousElement']
        ]);
    }
    
    handleKeyDown(event) {
        const key = event.key;
        const modifier = event.shiftKey ? 'Shift+' : '';
        const combination = modifier + key;
        
        if (this.shortcuts.has(combination)) {
            event.preventDefault();
            const action = this.shortcuts.get(combination);
            this.executeKeyboardAction(action);
        }
    }
    
    executeKeyboardAction(action) {
        switch (action) {
            case 'endTurn':
                this.gameStore.endTurn();
                break;
            case 'confirmAction':
                this.confirmCurrentAction();
                break;
            case 'navigateUp':
                this.moveSelection(0, -1);
                break;
            // ... other actions
        }
    }
}
```

### Screen Reader Support

```vue
<template>
  <div class="game-interface" role="application" aria-label="Turn-based Strategy Game">
    <!-- Live region for game updates -->
    <div aria-live="polite" aria-atomic="false" class="sr-only">
      {{ screenReaderFeedback }}
    </div>
    
    <!-- Game board with proper labeling -->
    <div 
      role="grid" 
      aria-label="Game Map"
      aria-rowcount="40"
      aria-colcount="40"
    >
      <div
        v-for="hex in visibleHexes"
        :key="hex.id"
        role="gridcell"
        :aria-label="getHexDescription(hex)"
        :aria-selected="selectedHex?.id === hex.id"
        tabindex="0"
      >
        <!-- Hex content -->
      </div>
    </div>
    
    <!-- Action panel with proper semantics -->
    <nav role="navigation" aria-label="Game Actions">
      <button
        v-for="action in availableActions"
        :key="action.id"
        :aria-describedby="`desc-${action.id}`"
        :disabled="!action.available"
      >
        {{ action.name }}
      </button>
    </nav>
  </div>
</template>
```

## 12.7 Performance Optimization

### Rendering Optimization

```javascript
class RenderOptimizer {
    constructor() {
        this.viewportHexes = new Set();
        this.renderQueue = [];
        this.lastFrameTime = 0;
        this.frameTarget = 16.67; // 60 FPS
    }
    
    updateViewport(cameraPosition, zoomLevel) {
        const newViewportHexes = this.calculateVisibleHexes(cameraPosition, zoomLevel);
        
        // Only update changed hexes
        const toAdd = newViewportHexes.filter(hex => !this.viewportHexes.has(hex));
        const toRemove = Array.from(this.viewportHexes).filter(hex => !newViewportHexes.has(hex));
        
        toRemove.forEach(hex => this.removeHexFromRender(hex));
        toAdd.forEach(hex => this.addHexToRender(hex));
        
        this.viewportHexes = newViewportHexes;
    }
    
    render(currentTime) {
        const deltaTime = currentTime - this.lastFrameTime;
        
        if (deltaTime >= this.frameTarget) {
            this.processRenderQueue();
            this.lastFrameTime = currentTime;
        }
        
        requestAnimationFrame(this.render.bind(this));
    }
}
```

### Component Lazy Loading

```javascript
// Lazy load heavy components
const TechTree = defineAsyncComponent(() => import('@/components/TechTree.vue'));
const DiplomacyPanel = defineAsyncComponent(() => import('@/components/DiplomacyPanel.vue'));
const ReplayViewer = defineAsyncComponent(() => import('@/components/ReplayViewer.vue'));

// Component registry with dynamic loading
export const componentRegistry = {
    'tech-tree': () => import('@/components/TechTree.vue'),
    'diplomacy-panel': () => import('@/components/DiplomacyPanel.vue'),
    'replay-viewer': () => import('@/components/ReplayViewer.vue')
};
```

---

## [GO BACK](0_CONTENT.md)