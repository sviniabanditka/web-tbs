# Section 4: Combat System and Modifiers

## 4.1 Combat Mechanics Overview

The combat system forms the core of military interactions in the game. Combat occurs at the individual unit and structure level, with each player turn including an attack phase where available AP can be used to engage enemy units or buildings. All combat calculations are performed automatically on the server, taking into account all modifiers and unit characteristics.

### Combat Philosophy
- **Tactical Depth**: Combat rewards positioning, unit composition, and terrain usage
- **Predictable Outcomes**: Players can calculate expected results before committing to combat
- **Risk vs Reward**: Aggressive tactics offer higher rewards but greater risks
- **Counter-Play**: Every unit type has strengths and weaknesses

## 4.2 Combat Resolution Process

### Combat Sequence
1. **Target Selection**: Player selects enemy unit or building to attack
2. **Range Verification**: Attack possible only if target is within attacker's range
3. **Damage Calculation**: Apply damage formula with all modifiers
4. **Damage Application**: Subtract result from target's health
5. **Experience Award**: Grant experience to participating units
6. **Destruction Check**: Remove units/buildings at 0 HP

### Damage Formula
The core damage calculation uses the following formula:

```
Final Damage = (Base Attack × Type Modifier × Terrain Modifier × Level Modifier) - Target Defense
```

**Minimum Damage**: At least 1 damage is always dealt (cannot be reduced to 0)

### Detailed Combat Example

**Scenario**: Veteran Infantry (Level 1) attacks Enemy Cavalry on Hills

**Calculation**:
- Base Attack: 25 (Infantry base)
- Type Modifier: 1.2 (Infantry vs Cavalry bonus)
- Terrain Modifier: 0.9 (attacking uphill penalty)
- Level Modifier: 1.1 (Level 1 = +10%)
- Target Defense: 10 (Cavalry base) + 5 (Hills bonus) = 15

**Result**: (25 × 1.2 × 0.9 × 1.1) - 15 = 29.7 - 15 = **14.7 → 15 damage**

## 4.3 Unit Type Modifiers

### Rock-Paper-Scissors Balance

| Attacker | vs Infantry | vs Cavalry | vs Archers | vs Siege |
|----------|-------------|------------|------------|----------|
| **Infantry** | 1.0 | 1.2 | 0.9 | 1.3 |
| **Cavalry** | 0.8 | 1.0 | 1.4 | 1.1 |
| **Archers** | 1.1 | 0.7 | 1.0 | 0.8 |
| **Siege** | 0.6 | 0.5 | 0.9 | 1.0 |

### Special Attack Types

#### Ranged Combat
- **Archers and Siege units** can attack from 2-3 hexes away
- **No retaliation** when attacking from maximum range
- **Line of sight** required (blocked by forests, mountains, buildings)
- **Ballistic trajectory** allows firing over level 1 obstacles

#### Melee Combat
- **Adjacent hex attacks** (1 hex range)
- **Mutual damage** - both attacker and defender can take damage
- **Charge bonus** - cavalry gets +25% damage if moved 3+ hexes this turn
- **Defensive stance** - units that didn't move get +15% defense

#### Siege Combat
- **Siege units** deal double damage to buildings and fortifications
- **Area effect** - siege attacks damage all units in target hex
- **Setup time** - siege units must remain stationary one turn before attacking
- **Vulnerable** - siege units take +50% damage from cavalry charges

## 4.4 Terrain Combat Modifiers

### Defensive Terrain Bonuses

| Terrain Type | Defense Bonus | Movement Penalty | Special Effects |
|--------------|---------------|------------------|-----------------|
| Plains | +0% | 0 AP | No modifiers |
| Forest | +25% | +1 AP | Blocks line of sight |
| Hills | +50% | +1 AP | +1 range for ranged units |
| Mountains | +75% | +2 AP | Impassable to siege units |
| Rivers | +30% | +1 AP | Blocks cavalry charge bonus |
| Marsh | +15% | +2 AP | Units lose 5 HP per turn |
| Desert | -10% | +1 AP | -1 HP per turn without water access |

### Elevation Advantages
- **Higher Ground**: +20% attack bonus when attacking from higher elevation
- **Lower Ground**: -15% attack penalty when attacking uphill
- **Equal Elevation**: No elevation modifiers apply

### Weather and Environmental Effects

#### Seasonal Weather (if implemented)
- **Rain**: -20% archer effectiveness, +10% forest defense
- **Snow**: +1 AP movement cost all terrain, -10% cavalry effectiveness  
- **Fog**: Reduced vision range by 1 hex, +25% ambush damage
- **Clear**: No weather effects, standard combat rules

## 4.5 Initiative and Combat Order

### Initiative System
When multiple units participate in combat, action order is determined by initiative:

```javascript
calculateInitiative(unit) {
    let initiative = unit.baseSpeed;
    initiative += unit.level * 2; // Experience bonus
    initiative += unit.getTechnologyBonuses(); // Tech improvements
    initiative -= unit.terrainPenalty; // Terrain effects
    
    return initiative + randomBonus(1, 6); // Dice roll element
}
```

### Initiative Values by Unit Type
- **Cavalry**: 8 base initiative (fastest)
- **Infantry**: 5 base initiative (balanced)
- **Archers**: 4 base initiative (moderate)
- **Siege**: 2 base initiative (slowest)

### Multiple Combat Resolution
When multiple units attack the same target:
1. **Simultaneous Declaration**: All attacks declared at once
2. **Initiative Order**: Resolve attacks from highest to lowest initiative
3. **Damage Accumulation**: Each attack applies damage sequentially
4. **Overkill Prevention**: Stop resolving attacks once target reaches 0 HP

## 4.6 Advanced Combat Mechanics

### Flanking and Positioning

#### Flanking Attacks
- **Surrounded**: Unit attacked from 3+ directions gets -25% defense
- **Rear Attack**: Attacking from directly opposite an ally gives +30% damage
- **Formation Fighting**: Adjacent friendly units provide +10% attack and defense

#### Zone of Control
- **Enemy Adjacent**: Units adjacent to enemies cannot move without penalty
- **Disengagement**: Moving away from adjacent enemy costs +1 AP
- **Free Strikes**: Disengaging units suffer automatic attack at -25% damage

### Morale and Psychology

#### Morale System
Units have morale levels affecting combat performance:

| Morale Level | Combat Modifier | Trigger Conditions |
|--------------|-----------------|-------------------|
| **Heroic** (+25%) | Recent major victory, defending home city |
| **High** (+10%) | Winning streak, well-supplied |
| **Normal** (0%) | Standard state |
| **Low** (-15%) | Recent defeats, low health |
| **Broken** (-30%) | Multiple defeats, isolated from support |

#### Morale Effects
- **Route**: Units at Broken morale may flee instead of fighting
- **Rally**: Units can recover morale through rest or victories
- **Leadership**: High-level units boost morale of nearby allies

### Special Abilities and Combat Skills

#### Unit-Specific Abilities

**Infantry Special Abilities**:
- _Shield Wall_: +50% defense vs ranged attacks, -1 movement
- _Bayonet Charge_: +40% attack vs cavalry if moved this turn
- _Entrenchment_: +25% defense if unit didn't move for 2 turns

**Cavalry Special Abilities**:
- _Cavalry Charge_: +100% damage if moved 4+ hexes this turn
- _Hit and Run_: Can move after attacking (costs +1 AP)
- _Pursuit_: Can attack fleeing enemies for free

**Archer Special Abilities**:
- _Aimed Shot_: +50% damage, +1 AP cost, must not move this turn
- _Volley Fire_: Attack all units in target hex, -25% damage each
- _Fire Arrows_: +100% damage vs buildings, chance to cause ongoing damage

**Siege Special Abilities**:
- _Bombardment_: Attack fortifications ignoring 50% of defense bonus
- _Breach_: Destroyed walls create permanent defense reduction
- _Indirect Fire_: Can attack targets without line of sight

## 4.7 Building Combat and Siege Warfare

### Building Defense Values

| Building Type | Health Points | Defense Rating | Special Defenses |
|---------------|---------------|----------------|------------------|
| Farm | 50 HP | 5 armor | None |
| Mine | 75 HP | 10 armor | None |
| Barracks | 100 HP | 15 armor | Can train emergency militia |
| Fortress | 200 HP | 25 armor | Returns fire at attackers |
| City Center | 150 HP | 20 armor | Heals adjacent friendly units |

### Siege Mechanics

#### Siege Process
1. **Approach**: Siege units must be adjacent to target building
2. **Setup**: One turn setup period for siege weapons
3. **Bombardment**: Multiple rounds of attacks to reduce building HP
4. **Breakthrough**: Building becomes non-functional at 50% HP
5. **Destruction**: Building is destroyed at 0 HP

#### City Siege Rules
- **Surrounded Cities**: Cannot produce units or generate resources
- **Supply Lines**: Cities cut off from friendly territory suffer -50% efficiency
- **Siege Duration**: Extended sieges cause civilian population to decline
- **Relief Forces**: Friendly units can break siege by defeating besiegers

### Fortification Systems

#### Wall Systems
- **Wooden Palisade**: +25% city defense, 75 HP, cheap construction
- **Stone Walls**: +50% city defense, 150 HP, expensive but durable
- **Star Fort**: +75% city defense, 200 HP, requires advanced technology

#### Tower Defense
- **Watchtowers**: +2 vision range, can attack adjacent enemies
- **Arrow Towers**: Automated defense, attacks nearest enemy within 3 hexes
- **Siege Towers**: Mobile siege platforms, +25% attack vs walls

## 4.8 Combat Analytics and Prediction

### Damage Prediction System
The game provides players with combat prediction before committing to attacks:

```javascript
class CombatPredictor {
    predictCombatOutcome(attacker, defender, terrain) {
        const minDamage = this.calculateDamage(attacker, defender, terrain, 'minimum');
        const maxDamage = this.calculateDamage(attacker, defender, terrain, 'maximum');
        const averageDamage = (minDamage + maxDamage) / 2;
        
        return {
            damageRange: [minDamage, maxDamage],
            averageDamage: averageDamage,
            surviveProbability: defender.currentHP > averageDamage ? 0.7 : 0.3,
            recommendedAction: this.getRecommendation(attacker, defender)
        };
    }
}
```

### Combat Statistics
Players can access detailed combat statistics:
- **Unit Kill/Death Ratios**: Track unit effectiveness over time
- **Terrain Combat Performance**: Analyze success rates by terrain type
- **Combat Efficiency**: Damage dealt vs damage received ratios
- **Experience Gains**: Track unit development through combat

---

## [GO BACK](0_CONTENT.md)