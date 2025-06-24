# Section 3: Game Mechanics

## 3.1 Action Points System

### Core Concept
Each player receives a fixed number of **Action Points (AP)** at the beginning of their turn. The base amount is **8 Action Points** per turn, which must be strategically allocated across various game actions. This system creates tactical tension and forces players to prioritize their objectives.

### Action Costs and Types

| Action Type | AP Cost | Description | Additional Requirements |
|-------------|---------|-------------|------------------------|
| Unit Movement | 1 AP/hex | Move one unit to adjacent hex | Valid path, no obstacles |
| Combat Attack | 2 AP | Attack enemy unit or building | Within attack range |
| Building Construction | 3-5 AP | Construct new buildings | Sufficient resources, valid location |
| Unit Recruitment | 2-4 AP | Recruit new military units | Available barracks, resources |
| Technology Research | 1 AP | Initiate research project | Available technology, science points |
| Diplomatic Action | 1-2 AP | Propose agreements, negotiations | Valid diplomatic target |
| Building Upgrade | 2-3 AP | Enhance existing structures | Upgrade prerequisites met |
| Resource Trading | 1 AP | Exchange resources with players | Active trade agreement |

### Advanced Action Point Mechanics

#### Action Point Banking
- Unused AP do not carry over to the next turn
- Players can end their turn early to trigger defensive bonuses
- Emergency actions can be taken with negative AP (penalties apply)

#### Efficiency Bonuses
Certain technologies and buildings can reduce AP costs:
- **Administrative Reform**: -1 AP for diplomatic actions
- **Military Engineering**: -1 AP for building construction
- **Logistics Network**: Units move 2 hexes for 1 AP (once per turn)

## 3.2 Hexagonal Grid System and Movement

### Grid Properties
The game world uses a **hexagonal grid** (hex grid) with **flat-top orientation**. The map size is **40×40 hexes** for 4-6 players, providing approximately 1,600 playable tiles.

#### Coordinate System
Each hex uses **cube coordinates** (q, r, s) where q + r + s = 0:
```javascript
class HexCoordinate {
    constructor(q, r, s) {
        this.q = q; // x-axis
        this.r = r; // y-axis  
        this.s = s; // z-axis
        
        if (q + r + s !== 0) {
            throw new Error("Invalid hex coordinates");
        }
    }
    
    // Distance calculation
    distanceTo(other) {
        return (Math.abs(this.q - other.q) + 
                Math.abs(this.r - other.r) + 
                Math.abs(this.s - other.s)) / 2;
    }
}
```

### Terrain Types and Effects

| Terrain Type | Movement Cost | Defense Modifier | Special Properties |
|--------------|---------------|------------------|--------------------|
| Plains | 1 AP | +0% | Standard terrain, no modifiers |
| Forest | +1 AP | +25% defense | Blocks line of sight |
| Hills | +1 AP | +50% defense | Enhanced archer range +1 hex |
| Mountains | +2 AP | +75% defense | Impassable to most units |
| Water | Impassable | N/A | Requires naval technology |
| Desert | +1 AP | -10% defense | Increased unit maintenance cost |
| Swamp | +2 AP | +15% defense | Units lose 5 HP per turn |

### Movement Mechanics

#### Basic Movement Rules
- Most units can move 2-5 hexes per turn based on their type
- Movement cost includes terrain penalties
- Units cannot pass through occupied hexes (friendly or enemy)
- Diagonal movement follows the same rules (no diagonal penalty)

#### Advanced Movement Features
- **Zone of Control**: Enemy units adjacent to your units have +1 AP movement cost
- **Road Networks**: Connected roads reduce movement cost by 50%
- **Forced March**: Units can move extra distance at the cost of 10 HP
- **Strategic Movement**: Units not in combat zones can move at reduced AP cost

## 3.3 Resource System

### Primary Resources

#### Gold 💰
- **Primary Use**: Unit recruitment, maintenance, trade, diplomacy
- **Generation**: Cities (2 base), farms (+2), trade routes (+1-3)
- **Storage**: Unlimited
- **Special Rules**: Required for unit upkeep (1 gold per unit per 5 turns)

#### Production 🔨
- **Primary Use**: Building construction, unit upgrades, infrastructure
- **Generation**: Cities (1 base), mines (+3), workshops (+2)
- **Storage**: Unlimited
- **Special Rules**: Can be converted to gold at 2:1 ratio

#### Science 🔬
- **Primary Use**: Technology research, unlocking new capabilities
- **Generation**: Cities (0 base), libraries (+2), universities (+4)
- **Storage**: Accumulates over time
- **Special Rules**: Research efficiency increases with total science per turn

#### Influence 🎭
- **Primary Use**: Diplomacy, cultural victory, city expansion
- **Generation**: Cities (0 base), temples (+1), cultural buildings (+2-3)
- **Storage**: Accumulates over time
- **Special Rules**: Influences diplomatic relations and trade efficiency

### Resource Generation Formula

```javascript
class ResourceCalculator {
    calculateTurnIncome(player) {
        const income = {
            gold: 0,
            production: 0,
            science: 0,
            influence: 0
        };
        
        // Base city income
        player.cities.forEach(city => {
            income.gold += 2;
            income.production += 1;
        });
        
        // Building bonuses
        player.buildings.forEach(building => {
            const bonus = building.getResourceBonus();
            income.gold += bonus.gold || 0;
            income.production += bonus.production || 0;
            income.science += bonus.science || 0;
            influence += bonus.influence || 0;
        });
        
        // Technology multipliers
        const techMultiplier = player.getTechnologyMultiplier();
        income.science *= techMultiplier;
        
        // Trade route bonuses
        income.gold += player.getTradeRouteIncome();
        
        return income;
    }
}
```

### Resource Management Strategies

#### Economic Specialization
Players can focus on specific resource types:
- **Gold Focus**: Rapid military expansion and mercenary tactics
- **Production Focus**: Strong infrastructure and defensive capabilities  
- **Science Focus**: Technological superiority and advanced units
- **Influence Focus**: Diplomatic victories and cultural dominance

#### Resource Trading
- Direct resource exchange between players (requires trade agreement)
- Market rates fluctuate based on supply and demand
- Emergency trading at 3:1 ratios (any resource to gold)

## 3.4 Unit System

### Unit Classifications

#### Infantry Units
- **Health**: 100 HP
- **Attack**: 25 damage
- **Defense**: 15 armor
- **Movement**: 3 hexes
- **Cost**: 3 gold, 2 AP
- **Special**: Defensive bonus in cities (+50% defense)

#### Cavalry Units  
- **Health**: 80 HP
- **Attack**: 35 damage  
- **Defense**: 10 armor
- **Movement**: 5 hexes
- **Cost**: 5 gold, 3 AP
- **Special**: Charge attack (+100% damage if moved 3+ hexes)

#### Archer Units
- **Health**: 60 HP
- **Attack**: 30 damage (ranged)
- **Defense**: 8 armor
- **Movement**: 2 hexes
- **Range**: 3 hexes
- **Cost**: 4 gold, 2 AP
- **Special**: No melee penalty, can attack over obstacles

#### Siege Units
- **Health**: 120 HP
- **Attack**: 60 damage vs buildings (30 vs units)
- **Defense**: 20 armor
- **Movement**: 1 hex
- **Range**: 2 hexes
- **Cost**: 8 gold, 4 AP
- **Special**: Devastating against fortifications

### Experience and Promotion System

#### Experience Gain
Units gain experience through combat and survival:
- **Victory in Combat**: 25-50 XP (based on enemy strength)
- **Surviving Combat**: 10-20 XP
- **Defending Successfully**: 15-30 XP
- **Completing Objectives**: 20-40 XP

#### Promotion Levels
Each unit can achieve up to **3 promotion levels**:

**Level 1 (100 XP)**:
- +10% to all combat stats
- Choose one specialization:
  - _Veteran_: +20% defense
  - _Aggressive_: +20% attack
  - _Mobile_: +1 movement range

**Level 2 (250 XP total)**:
- +20% to all combat stats
- Unlock special abilities:
  - _Second Wind_: Heal 25 HP once per battle
  - _Leadership_: Adjacent friendly units get +10% attack
  - _Entrench_: +50% defense when not moving

**Level 3 (500 XP total)**:
- +30% to all combat stats
- Master-level abilities:
  - _Elite Training_: Can attack twice per turn
  - _Tactical Genius_: Ignore terrain movement penalties
  - _Inspiration_: All friendly units within 2 hexes get +15% attack

### Unit Maintenance and Logistics

#### Upkeep Costs
- **Base Upkeep**: 1 gold per unit every 5 turns
- **Distance Penalty**: +1 gold per unit more than 10 hexes from nearest city
- **Promotion Bonus**: Higher level units cost +1 gold per level

#### Supply Lines
- Units more than 5 hexes from friendly territory suffer -10% combat effectiveness
- Units can forage in neutral territory (50% chance of gaining 1 gold per turn)
- Supply wagons can extend effective range by 3 hexes

## 3.5 Building System

### Building Categories

#### Economic Buildings

**Farm**
- **Effect**: +2 gold per turn
- **Cost**: 4 production, 3 AP
- **Build Time**: 2 turns
- **Upgrade**: Level 2 (+3 gold), Level 3 (+4 gold)

**Mine**
- **Effect**: +3 production per turn
- **Cost**: 6 production, 4 AP
- **Build Time**: 3 turns
- **Requirement**: Must be built on hills or mountains

**Trade Post**
- **Effect**: +1 gold per active trade route
- **Cost**: 5 production, 3 AP
- **Build Time**: 2 turns
- **Special**: Enables trade routes with other players

#### Military Buildings

**Barracks**
- **Effect**: Enables infantry and cavalry recruitment
- **Cost**: 8 production, 4 AP
- **Build Time**: 3 turns
- **Bonus**: New units start with +25% experience

**Archery Range**
- **Effect**: Enables archer recruitment, +1 range to city defense
- **Cost**: 6 production, 3 AP
- **Build Time**: 2 turns

**Fortress**
- **Effect**: +50% defense to units in city, +2 city bombardment range
- **Cost**: 12 production, 5 AP
- **Build Time**: 4 turns
- **Special**: Can attack adjacent enemy units automatically

#### Scientific Buildings

**Library**
- **Effect**: +2 science per turn
- **Cost**: 6 production, 4 AP
- **Build Time**: 2 turns
- **Bonus**: Reduces technology research time by 10%

**University**  
- **Effect**: +4 science per turn
- **Cost**: 12 production, 5 AP
- **Build Time**: 4 turns
- **Requirement**: Must have Library
- **Special**: Unlocks advanced technologies

#### Cultural Buildings

**Temple**
- **Effect**: +2 influence per turn
- **Cost**: 5 production, 3 AP
- **Build Time**: 2 turns
- **Special**: Improves diplomatic relations with all players

**Theater**
- **Effect**: +3 influence per turn, cultural defense bonus
- **Cost**: 8 production, 4 AP
- **Build Time**: 3 turns
- **Special**: Prevents enemy cultural influence

### Building Mechanics

#### Construction Process
1. **Initiation**: Player selects building type and location
2. **Resource Check**: Verify sufficient production and AP
3. **Placement Validation**: Ensure valid location and prerequisites
4. **Construction Queue**: Building enters production queue
5. **Completion**: Building becomes active after specified turns

#### Building Upgrades
- Most buildings can be upgraded 2 levels maximum
- Each upgrade increases effectiveness by 50%
- Upgrade cost is 75% of original construction cost
- Upgrades take 50% of original construction time

#### Building Destruction and Repair
- Buildings can be damaged by siege weapons
- Damaged buildings operate at reduced efficiency
- Repair costs 25% of original construction cost
- Completely destroyed buildings must be rebuilt from scratch

## 3.6 Technology Research System

### Research Mechanics

#### Research Initiation
- Player selects available technology from tech tree
- Spends 1 AP to begin research
- Science points are deducted over multiple turns until completion

#### Research Speed Formula
```javascript
calculateResearchTime(technology) {
    const baseCost = technology.scienceCost;
    const sciencePerTurn = player.getScienceIncome();
    const technologyBonus = player.getResearchBonus();
    
    const adjustedCost = baseCost * (1 - technologyBonus);
    const turnsRequired = Math.ceil(adjustedCost / sciencePerTurn);
    
    return Math.max(turnsRequired, technology.minimumTurns);
}
```

#### Research Bonuses
- **Libraries**: -10% research time
- **Universities**: -20% research time
- **Cultural Exchange**: -15% research time with allied players
- **Scientific Method**: -25% research time for all technologies

### Technology Dependencies
Technologies are organized in a dependency tree:
- **Prerequisites**: Required technologies must be completed first
- **Synergies**: Some technologies provide bonuses when combined
- **Exclusive Paths**: Certain technology branches lock out others

---

## [GO BACK](0_CONTENT.md)