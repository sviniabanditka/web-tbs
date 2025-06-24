# Section 9: Unit and Building Statistics Summary

## 9.1 Complete Unit Statistics

### Basic Unit Types

| Unit Type | Health | Attack | Defense | Movement | Range | Vision | Recruitment Cost | Recruitment AP | Maintenance |
|-----------|--------|--------|---------|----------|-------|--------|------------------|----------------|-------------|
| **Infantry** | 100 HP | 25 dmg | 15 armor | 3 hexes | 1 hex | 2 hexes | 3 gold | 2 AP | 1 gold/5 turns |
| **Cavalry** | 80 HP | 35 dmg | 10 armor | 5 hexes | 1 hex | 3 hexes | 5 gold | 3 AP | 1 gold/5 turns |
| **Archers** | 60 HP | 30 dmg | 8 armor | 2 hexes | 3 hexes | 2 hexes | 4 gold | 2 AP | 1 gold/5 turns |
| **Siege** | 120 HP | 60 dmg* | 20 armor | 1 hex | 2 hexes | 1 hex | 8 gold | 4 AP | 2 gold/5 turns |

*Siege units deal double damage (120) to buildings and fortifications

### Unit Type Effectiveness Matrix

| Attacker vs Defender | Infantry | Cavalry | Archers | Siege | Buildings |
|---------------------|----------|---------|---------|-------|-----------|
| **Infantry** | 1.0x | 1.2x | 0.9x | 1.3x | 0.5x |
| **Cavalry** | 0.8x | 1.0x | 1.4x | 1.1x | 0.3x |
| **Archers** | 1.1x | 0.7x | 1.0x | 0.8x | 0.7x |
| **Siege** | 0.6x | 0.5x | 0.9x | 1.0x | 2.0x |

### Advanced Unit Variations (Future Expansion)

| Unit Type | Health | Attack | Defense | Movement | Special Abilities | Cost | Prerequisites |
|-----------|--------|--------|---------|----------|-------------------|------|---------------|
| **Elite Infantry** | 120 HP | 35 dmg | 20 armor | 3 hexes | +1 attack per adjacent ally | 6 gold, 3 AP | Iron Working |
| **Heavy Cavalry** | 100 HP | 45 dmg | 15 armor | 4 hexes | Charge: +50% damage after 3+ move | 8 gold, 4 AP | Horseback Riding |
| **Crossbowmen** | 75 HP | 40 dmg | 12 armor | 2 hexes | Ignore 25% of armor | 6 gold, 3 AP | Engineering |
| **Catapult** | 100 HP | 50 dmg | 15 armor | 1 hex | Area attack: damage adjacent hexes | 10 gold, 5 AP | Mathematics |
| **Scout** | 40 HP | 15 dmg | 5 armor | 4 hexes | Stealth: harder to detect | 3 gold, 1 AP | None |

## 9.2 Unit Promotion System

### Experience Requirements and Bonuses

| Promotion Level | XP Required | Stat Bonus | Special Abilities Available |
|----------------|-------------|------------|----------------------------|
| **Recruit (0)** | 0 XP | Base stats | None |
| **Veteran (1)** | 100 XP | +10% all stats | Choose 1: Tough (+20% HP), Aggressive (+20% attack), Mobile (+1 movement) |
| **Elite (2)** | 250 XP total | +20% all stats | Choose 1: Second Wind (heal 25 HP/battle), Leadership (+10% attack to adjacent allies), Entrench (+50% defense when not moving) |
| **Legendary (3)** | 500 XP total | +30% all stats | Choose 1: Double Strike (attack twice), March Master (ignore terrain penalties), Inspire (+15% attack to all units within 2 hexes) |

### Experience Gain Sources

| Action | XP Reward | Conditions |
|--------|-----------|------------|
| **Destroy Enemy Unit** | 50 XP | Full XP for destroying unit |
| **Survive Combat** | 15 XP | Per combat survived |
| **Deal Damage** | 1 XP per 10 damage | Damage dealt to enemies |
| **Defend City** | 25 XP | Successfully defend against siege |
| **Capture City** | 40 XP | Participate in city capture |
| **Win Battle** | 20 XP | Survive winning battle |

## 9.3 Building Statistics and Effects

### Economic Buildings

| Building | Construction Cost | Build Time | Effect | Upgrade Cost | Upgrade Effect | Prerequisites |
|----------|------------------|------------|--------|--------------|----------------|---------------|
| **Farm** | 4 production, 3 AP | 2 turns | +2 gold/turn | 3 production, 2 AP | +3 gold/turn | None |
| **Mine** | 6 production, 4 AP | 3 turns | +3 production/turn | 4 production, 3 AP | +5 production/turn | Must be on hills/mountains |
| **Trade Post** | 5 production, 3 AP | 2 turns | +1 gold per trade route | 4 production, 2 AP | +2 gold per trade route | None |
| **Market** | 8 production, 4 AP | 3 turns | +15% gold income, enables trading | 6 production, 3 AP | +25% gold income | Currency technology |
| **Bank** | 12 production, 5 AP | 4 turns | +20% gold income, +2 gold/turn | 9 production, 4 AP | +30% gold income, +4 gold/turn | Banking technology |

### Military Buildings

| Building | Construction Cost | Build Time | Effect | Upgrade Cost | Upgrade Effect | Prerequisites |
|----------|------------------|------------|--------|--------------|----------------|---------------|
| **Barracks** | 8 production, 4 AP | 3 turns | Enables infantry/cavalry recruitment | 6 production, 3 AP | New units start with 25% XP | None |
| **Archery Range** | 6 production, 3 AP | 2 turns | Enables archer recruitment | 4 production, 2 AP | +1 range for city defense | None |
| **Siege Workshop** | 10 production, 5 AP | 4 turns | Enables siege unit recruitment | 8 production, 4 AP | -25% siege unit cost | Engineering |
| **Fortress** | 12 production, 5 AP | 4 turns | +50% defense for units in city | 9 production, 4 AP | +75% defense, bombard adjacent enemies | None |
| **Walls** | 8 production, 4 AP | 3 turns | +25% city defense, 100 HP | 6 production, 3 AP | +50% city defense, 150 HP | Masonry |

### Scientific Buildings

| Building | Construction Cost | Build Time | Effect | Upgrade Cost | Upgrade Effect | Prerequisites |
|----------|------------------|------------|--------|--------------|----------------|---------------|
| **Library** | 6 production, 4 AP | 2 turns | +2 science/turn | 4 production, 3 AP | +3 science/turn | Writing |
| **University** | 12 production, 5 AP | 4 turns | +4 science/turn, -10% research time | 9 production, 4 AP | +6 science/turn, -15% research time | Library required |
| **Observatory** | 15 production, 6 AP | 5 turns | +6 science/turn, +2 vision range | 12 production, 5 AP | +9 science/turn, +3 vision range | Astronomy |
| **Laboratory** | 18 production, 7 AP | 6 turns | +8 science/turn, enables advanced research | 15 production, 6 AP | +12 science/turn, -20% research time | Scientific Method |

### Cultural Buildings

| Building | Construction Cost | Build Time | Effect | Upgrade Cost | Upgrade Effect | Prerequisites |
|----------|------------------|------------|--------|--------------|----------------|---------------|
| **Temple** | 5 production, 3 AP | 2 turns | +2 influence/turn | 4 production, 2 AP | +3 influence/turn | None |
| **Theater** | 8 production, 4 AP | 3 turns | +3 influence/turn, cultural defense | 6 production, 3 AP | +5 influence/turn, stronger defense | Drama |
| **Monument** | 10 production, 5 AP | 4 turns | +4 influence/turn, +1 city influence radius | 8 production, 4 AP | +6 influence/turn, +2 influence radius | Arts |
| **Palace** | 20 production, 8 AP | 6 turns | +6 influence/turn, diplomatic bonuses | 15 production, 6 AP | +9 influence/turn, enhanced diplomacy | Monarchy |

## 9.4 Building Health and Destruction

### Building Durability

| Building Category | Base Health | Armor | Repair Cost | Repair Time |
|------------------|-------------|-------|-------------|-------------|
| **Economic (Tier 1)** | 50 HP | 5 armor | 25% of build cost | 1 turn |
| **Economic (Tier 2)** | 75 HP | 8 armor | 25% of build cost | 2 turns |
| **Military (Basic)** | 100 HP | 15 armor | 30% of build cost | 2 turns |
| **Military (Advanced)** | 150 HP | 25 armor | 30% of build cost | 3 turns |
| **Scientific** | 80 HP | 10 armor | 25% of build cost | 2 turns |
| **Cultural** | 60 HP | 8 armor | 20% of build cost | 1 turn |
| **City Center** | 200 HP | 20 armor | 50% of build cost | 4 turns |

### Siege Effectiveness vs Buildings

| Siege Unit Type | vs Economic | vs Military | vs Scientific | vs Cultural | vs City Center |
|----------------|-------------|-------------|---------------|-------------|----------------|
| **Basic Siege** | 2.0x damage | 1.5x damage | 2.0x damage | 2.0x damage | 1.0x damage |
| **Catapult** | 2.5x damage | 2.0x damage | 2.5x damage | 2.5x damage | 1.5x damage |
| **Trebuchet** | 3.0x damage | 2.5x damage | 3.0x damage | 3.0x damage | 2.0x damage |

## 9.5 Resource Generation Summary

### Base Resource Income

| Source | Gold | Production | Science | Influence | Notes |
|--------|------|------------|---------|-----------|-------|
| **City (Base)** | +2 | +1 | +0 | +0 | Every city provides base income |
| **Population Growth** | +1 per 5 pop | +0 | +0 | +1 per 10 pop | Cities grow over time |
| **Farm** | +2 | +0 | +0 | +0 | Basic economic building |
| **Mine** | +0 | +3 | +0 | +0 | Must be on mineral deposits |
| **Trade Route** | +2 | +0 | +0 | +1 | Requires trade agreement |
| **Library** | +0 | +0 | +2 | +0 | Basic scientific building |
| **Temple** | +0 | +0 | +0 | +2 | Basic cultural building |

### Technology Multipliers

| Technology | Resource Affected | Multiplier | Additional Effect |
|------------|------------------|------------|-------------------|
| **Currency** | Gold | +20% | Enables advanced trade |
| **Engineering** | Production | +15% | Reduces build times |
| **Mathematics** | Science | +25% | Unlocks advanced technologies |
| **Philosophy** | Influence | +20% | Improves diplomatic relations |
| **Agriculture** | Gold (from farms) | +50% | Farms provide +3 instead of +2 |
| **Mining** | Production (from mines) | +40% | Mines provide +4 instead of +3 |

## 9.6 Combat Modifiers and Terrain Effects

### Terrain Combat Modifiers

| Terrain Type | Defense Bonus | Movement Cost | Attack Modifier | Special Effects |
|--------------|---------------|---------------|-----------------|-----------------|
| **Plains** | +0% | 1 AP | 1.0x | No modifiers |
| **Forest** | +25% | +1 AP | 0.9x (attacker) | Blocks line of sight |
| **Hills** | +50% | +1 AP | 1.1x (defender) | +1 range for ranged units |
| **Mountains** | +75% | +2 AP | 0.8x (attacker) | Impassable to siege units |
| **River** | +30% | +1 AP | 0.9x (attacker) | Blocks cavalry charge |
| **Marsh** | +15% | +2 AP | 0.8x (all units) | -5 HP per turn |
| **Desert** | -10% | +1 AP | 0.9x (all units) | -1 HP per turn without water |

### Weather Effects (Optional Advanced Feature)

| Weather | Vision | Movement | Combat | Duration |
|---------|--------|----------|--------|----------|
| **Clear** | Normal | Normal | Normal | Permanent |
| **Rain** | -1 hex | +1 AP all terrain | -20% archer effectiveness | 3-5 turns |
| **Snow** | -1 hex | +1 AP all terrain | -10% cavalry effectiveness | 4-8 turns |
| **Fog** | -2 hexes | Normal | +25% ambush damage | 2-3 turns |
| **Storm** | -2 hexes | +2 AP all terrain | No ranged combat | 1-2 turns |

## 9.7 Cost-Benefit Analysis Tables

### Unit Cost-Effectiveness

| Unit Type | Gold per HP | Gold per Attack | Attack per AP | Overall Rating |
|-----------|-------------|-----------------|---------------|----------------|
| **Infantry** | 0.03 | 0.12 | 12.5 | A (Balanced) |
| **Cavalry** | 0.063 | 0.143 | 11.7 | B+ (Mobile) |
| **Archers** | 0.067 | 0.133 | 15.0 | A- (Range advantage) |
| **Siege** | 0.067 | 0.133 | 15.0 | B (Specialized) |

### Building Return on Investment

| Building | Turns to Break Even | Production Efficiency | Long-term Value |
|----------|---------------------|----------------------|-----------------|
| **Farm** | 2 turns | High | A+ |
| **Mine** | 2 turns | Very High | A+ |
| **Library** | 3 turns | Medium | A (if pursuing science) |
| **Barracks** | N/A (military utility) | N/A | A (military essential) |
| **Market** | 4-5 turns | Medium | B+ |

---

## [GO BACK](0_CONTENT.md)