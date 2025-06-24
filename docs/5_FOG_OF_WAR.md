# Section 5: Fog of War and Visibility System

## 5.1 Fog of War Concept

**Fog of War** is a critical game mechanic that limits player visibility to only those territories within the operational range of their units and buildings. This system creates uncertainty, forces active reconnaissance, and requires players to plan their actions considering potential threats and hidden opponents.

### Core Principles
- **Information Warfare**: Knowledge becomes a strategic resource
- **Exploration Incentive**: Players must actively scout to gather intelligence
- **Defensive Positioning**: Strategic placement of units for vision control
- **Uncertainty Management**: Decision-making under incomplete information

## 5.2 Visibility Mechanics

### Vision Range System

Each unit and building has a specific **vision range** determining which hexes the player can see in real-time:

| Unit/Building Type | Vision Range (Hexes) | Special Properties |
|-------------------|----------------------|--------------------|
| Infantry | 2 | Standard vision, enhanced in forests |
| Cavalry | 3 | Mobile reconnaissance capability |
| Archers | 2 | Elevated position bonus on hills |
| Siege Units | 1 | Limited vision due to equipment |
| Scout (future unit) | 4 | Specialized reconnaissance unit |
| Cities | 3 | Central observation point |
| Watchtowers | 5 | Dedicated surveillance structure |
| Lighthouses | 6 | Coastal and naval visibility |

### Vision Calculation Algorithm

```javascript
class VisionSystem {
    calculateVisibleHexes(unit, gameMap) {
        const visibleHexes = new Set();
        const centerHex = unit.position;
        const range = unit.getVisionRange();
        
        // Get all hexes within range
        for (let hex of gameMap.getHexesInRange(centerHex, range)) {
            if (this.hasLineOfSight(centerHex, hex, gameMap)) {
                visibleHexes.add(hex);
                
                // Apply terrain-specific vision rules
                this.applyTerrainVisionEffects(hex, visibleHexes, gameMap);
            }
        }
        
        return visibleHexes;
    }
    
    hasLineOfSight(from, to, gameMap) {
        const path = gameMap.getLineBetween(from, to);
        
        for (let hex of path) {
            if (this.isVisionBlocking(hex, gameMap)) {
                return false;
            }
        }
        
        return true;
    }
}
```

### Line of Sight Rules

#### Vision Blocking Terrain
- **Forests**: Block vision completely (except for units inside)
- **Mountains**: Block vision completely, except from elevated positions
- **Buildings**: Large buildings block vision behind them
- **Hills**: Partial blocking - reduce vision range by 1 hex

#### Vision Enhancement
- **Elevated Positions**: Units on hills get +1 vision range
- **Clear Weather**: No vision penalties
- **Coastal Position**: Naval units get enhanced ocean visibility

## 5.3 Explored vs. Visible Territory

### Territory States

#### Unexplored (Black)
- **Status**: Never visited by player units
- **Information Available**: None
- **Visual Representation**: Completely black/hidden areas

#### Explored (Gray/Dimmed)
- **Status**: Previously visited but not currently visible
- **Information Available**: Terrain type, last known state
- **Information Hidden**: Current enemy units, new buildings, resource changes
- **Visual Representation**: Dimmed/grayscale terrain

#### Visible (Full Color)
- **Status**: Currently within vision range of player units/buildings
- **Information Available**: Complete real-time information
- **Visual Representation**: Full color, all details visible

### Information Persistence

```javascript
class TerritoryInformation {
    constructor() {
        this.exploredHexes = new Map(); // hex -> last seen state
        this.visibleHexes = new Set();  // currently visible hexes
    }
    
    updateExploredHex(hex, gameState) {
        this.exploredHexes.set(hex.coordinates, {
            terrain: hex.terrain,
            lastSeen: gameState.currentTurn,
            knownBuildings: hex.buildings.slice(),
            knownResources: hex.resources.slice()
        });
    }
    
    getHexInformation(hex) {
        if (this.isVisible(hex)) {
            return hex.getCurrentState(); // Real-time information
        } else if (this.isExplored(hex)) {
            return this.exploredHexes.get(hex.coordinates); // Historical data
        } else {
            return null; // Unknown territory
        }
    }
}
```

## 5.4 Strategic Impact of Fog of War

### Reconnaissance Strategy

#### Active Scouting
- **Scout Patrols**: Dedicated units for exploration and intelligence gathering
- **Forward Observers**: Positioning units to monitor enemy territory
- **Watchtower Networks**: Building surveillance infrastructure

#### Intelligence Gathering
- **Enemy Movement Tracking**: Predicting opponent actions based on partial information
- **Resource Assessment**: Identifying valuable territories for expansion
- **Threat Detection**: Early warning systems for incoming attacks

### Defensive Applications

#### Hidden Positioning
- **Ambush Tactics**: Concealing units for surprise attacks
- **Defensive Preparations**: Hidden fortifications and troop concentrations
- **Strategic Reserves**: Keeping relief forces out of enemy sight

#### Misdirection
- **Feint Attacks**: Visible movements to disguise real intentions  
- **Decoy Positions**: False concentrations to mislead opponents
- **Information Warfare**: Controlling what enemies can observe

## 5.5 Advanced Visibility Features

### Dynamic Vision Modifiers

#### Weather Effects (if implemented)
- **Fog**: Reduces all vision ranges by 1 hex
- **Rain**: Reduces vision by 25%
- **Snow**: Reduces vision by 15%, but increases tracking of enemy movements
- **Clear Skies**: Standard vision ranges apply

#### Day/Night Cycles (optional feature)
- **Dawn/Dusk**: Reduced vision ranges (-1 hex)
- **Night**: Severely reduced vision (-2 hexes), but stealth bonuses
- **Day**: Full vision ranges, no penalties

### Technology-Enhanced Vision

#### Technological Improvements
- **Optics**: +1 vision range for archer units
- **Cartography**: Explored territories reveal more detailed information
- **Telecommunications**: Instant information sharing between cities
- **Radar** (late game): Detects all units within large radius

## 5.6 Multiplayer Considerations

### Shared Vision Systems

#### Allied Vision
- **Military Alliance**: Share vision in agreed-upon regions
- **Intelligence Sharing**: Periodic information exchange
- **Joint Operations**: Temporary vision sharing during coordinated attacks

#### Information Trading
- **Map Trading**: Exchange explored territory information
- **Intelligence Reports**: Sell information about enemy positions
- **Reconnaissance Contracts**: Hire allies for scouting missions

### Anti-Intelligence Measures

#### Stealth and Concealment
- **Forest Movement**: Units in forests are harder to detect
- **Night Operations**: Reduced chance of detection during night turns
- **Camouflage**: Special units with reduced detection radius

#### Counter-Intelligence
- **False Information**: Ability to plant misleading intelligence
- **Communication Interception**: Chance to intercept enemy diplomatic messages
- **Disinformation**: Spread false reports about unit positions

## 5.7 Technical Implementation

### Client-Side Rendering

```javascript
class FogOfWarRenderer {
    constructor(gameMap) {
        this.gameMap = gameMap;
        this.visibilityLayers = {
            unexplored: new CanvasLayer(),
            explored: new CanvasLayer(),
            visible: new CanvasLayer()
        };
    }
    
    updateVisibility(playerVision) {
        // Clear previous visibility
        this.clearVisibilityLayers();
        
        // Render based on visibility state
        for (let hex of this.gameMap.getAllHexes()) {
            const state = playerVision.getHexVisibility(hex);
            
            switch (state) {
                case 'visible':
                    this.renderVisibleHex(hex);
                    break;
                case 'explored':
                    this.renderExploredHex(hex);
                    break;
                case 'unexplored':
                    this.renderUnexploredHex(hex);
                    break;
            }
        }
    }
}
```

### Server-Side Data Filtering

```php
class VisionController
{
    public function getVisibleGameState(Player $player, Game $game): array
    {
        $visibleHexes = $this->calculatePlayerVision($player, $game);
        $gameState = [];
        
        foreach ($game->getMapHexes() as $hex) {
            if ($this->isHexVisible($hex, $visibleHexes)) {
                $gameState[] = $hex->getCompleteState();
            } elseif ($this->isHexExplored($hex, $player)) {
                $gameState[] = $hex->getLastKnownState($player);
            }
            // Unexplored hexes are not included in response
        }
        
        return $gameState;
    }
    
    private function isHexVisible(Hex $hex, Collection $visibleHexes): bool
    {
        return $visibleHexes->contains(function ($visibleHex) use ($hex) {
            return $visibleHex->coordinates->equals($hex->coordinates);
        });
    }
}
```

### Performance Optimization

#### Visibility Caching
- **Static Vision**: Cache vision ranges for immobile buildings
- **Movement Prediction**: Pre-calculate vision changes for common movements
- **Incremental Updates**: Only recalculate vision for changed areas

#### Client-Side Prediction
- **Local Calculation**: Client predicts vision changes before server confirmation
- **Smooth Transitions**: Gradual revelation/concealment of terrain
- **Bandwidth Optimization**: Only send vision changes, not complete state

## 5.8 Balance and Design Considerations

### Information Balance
- **Exploration Rewards**: Provide meaningful advantages for active scouting
- **Vision Costs**: Balance vision range against other unit capabilities
- **Defensive Options**: Ensure players can protect against excessive reconnaissance

### Player Experience
- **Clear Feedback**: Visual indicators for different visibility states
- **Information Management**: UI tools for tracking explored territories
- **Strategic Depth**: Meaningful choices about information gathering vs. other activities

### Accessibility Features
- **Colorblind Support**: Alternative visual indicators beyond color
- **Audio Cues**: Sound effects for vision state changes
- **Customizable UI**: Player options for fog of war visualization

---

## [GO BACK](0_CONTENT.md)