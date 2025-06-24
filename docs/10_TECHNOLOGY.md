# Section 10: Technology Tree and Effects

## 10.1 Technology System Overview

The **Technology Tree** represents the advancement of civilization through research and discovery. Technologies are organized into four distinct branches: Military, Economic, Scientific, and Cultural. Each branch offers unique advantages and unlocks different paths to victory.

### Research Mechanics
- **Science Cost**: Each technology requires specific science points to complete
- **Research Time**: Technologies take multiple turns to research based on science income
- **Prerequisites**: Most technologies require prior technologies to unlock
- **Branching Paths**: Some technologies open exclusive development paths

### Technology Categories

```javascript
const technologyCategories = {
    MILITARY: {
        color: '#dc2626', // Red
        focus: 'Combat effectiveness and military units',
        victoryPath: 'Military conquest'
    },
    ECONOMIC: {
        color: '#f59e0b', // Amber
        focus: 'Resource generation and trade',
        victoryPath: 'Economic dominance'
    },
    SCIENTIFIC: {
        color: '#3b82f6', // Blue
        focus: 'Research speed and advanced technologies',
        victoryPath: 'Scientific supremacy'
    },
    CULTURAL: {
        color: '#8b5cf6', // Purple
        focus: 'Diplomacy and influence generation',
        victoryPath: 'Cultural victory'
    }
};
```

## 10.2 Military Technology Branch

### Early Military Technologies

#### Bronze Working
- **Science Cost**: 15 points
- **Research Time**: 3-5 turns (early game)
- **Prerequisites**: None
- **Effects**:
  - Unlocks advanced infantry units
  - +10% attack for all melee units
  - Enables bronze weapons production
- **Unlocks**: Iron Working, Military Engineering

#### Archery
- **Science Cost**: 12 points
- **Research Time**: 2-4 turns
- **Prerequisites**: None
- **Effects**:
  - Unlocks archer units
  - +1 range for all ranged units
  - Enables hunting for additional food
- **Unlocks**: Engineering, Siege Warfare

#### Animal Husbandry
- **Science Cost**: 18 points
- **Research Time**: 3-5 turns
- **Prerequisites**: None
- **Effects**:
  - Unlocks cavalry units
  - +20% movement speed for mounted units
  - Enables horse resources utilization
- **Unlocks**: Horseback Riding, Military Logistics

### Advanced Military Technologies

#### Iron Working
- **Science Cost**: 30 points
- **Research Time**: 5-8 turns
- **Prerequisites**: Bronze Working
- **Effects**:
  - +15% attack for all units
  - +10% defense for all units
  - Unlocks iron-based weapons and tools
  - Enables advanced military buildings
- **Unlocks**: Steel Working, Military Engineering

#### Engineering
- **Science Cost**: 35 points
- **Research Time**: 6-9 turns
- **Prerequisites**: Archery
- **Effects**:
  - Unlocks siege weapons (catapults, ballistas)
  - +50% damage against fortifications
  - -1 AP cost for building construction
  - Enables advanced infrastructure
- **Unlocks**: Gunpowder, Architecture

#### Military Logistics
- **Science Cost**: 40 points
- **Research Time**: 7-10 turns
- **Prerequisites**: Animal Husbandry, Bronze Working
- **Effects**:
  - +1 movement for all military units
  - Reduced unit maintenance costs (-25%)
  - Enables supply lines and faster deployment
  - Units can move after attacking (cavalry only)
- **Unlocks**: Professional Army

### Late-Game Military Technologies

#### Gunpowder
- **Science Cost**: 60 points
- **Research Time**: 10-15 turns
- **Prerequisites**: Engineering, Chemistry
- **Effects**:
  - Unlocks firearms and cannons
  - +100% siege damage against walls
  - Renders traditional fortifications less effective
  - Enables revolutionary military tactics
- **Unlocks**: Artillery, Military Revolution

#### Professional Army
- **Science Cost**: 80 points
- **Research Time**: 12-18 turns
- **Prerequisites**: Military Logistics, Iron Working
- **Effects**:
  - All new units start with 1 promotion level
  - +25% experience gain for all units
  - Enables military academies
  - Standing army maintenance costs reduced
- **Unlocks**: Modern Warfare

## 10.3 Economic Technology Branch

### Early Economic Technologies

#### Agriculture
- **Science Cost**: 10 points
- **Research Time**: 2-3 turns
- **Prerequisites**: None
- **Effects**:
  - +50% gold from farms (+3 instead of +2)
  - Enables advanced farming techniques
  - Supports larger population growth
  - Unlocks food surplus mechanisms
- **Unlocks**: Irrigation, Animal Husbandry

#### Mining
- **Science Cost**: 15 points
- **Research Time**: 3-4 turns
- **Prerequisites**: None
- **Effects**:
  - +40% production from mines (+4 instead of +3)
  - Enables extraction of rare materials
  - Unlocks advanced mining buildings
  - Reveals mineral deposits on map
- **Unlocks**: Metallurgy, Engineering

#### Pottery
- **Science Cost**: 8 points
- **Research Time**: 2-3 turns
- **Prerequisites**: None
- **Effects**:
  - Enables granaries (+20% city growth)
  - Unlocks basic crafting and trade goods
  - +10% gold income from cities
  - Cultural preservation benefits
- **Unlocks**: Currency, Writing

### Intermediate Economic Technologies

#### Currency
- **Science Cost**: 25 points
- **Research Time**: 4-6 turns
- **Prerequisites**: Pottery
- **Effects**:
  - +25% gold income from all sources
  - Enables markets and financial buildings
  - Unlocks advanced trade mechanisms
  - Reduces transaction costs in diplomacy
- **Unlocks**: Banking, Trade Networks

#### Trade Networks
- **Science Cost**: 35 points
- **Research Time**: 6-8 turns
- **Prerequisites**: Currency, Navigation (if coastal)
- **Effects**:
  - +100% income from trade routes
  - Enables intercontinental trade
  - Reduces diplomatic costs with trading partners
  - Unlocks merchant units
- **Unlocks**: Mercantilism, Economics

#### Irrigation
- **Science Cost**: 30 points
- **Research Time**: 5-7 turns
- **Prerequisites**: Agriculture, Engineering
- **Effects**:
  - Farms can be built on desert and dry terrain
  - +20% food production from all sources
  - Enables aqueducts and water management
  - Supports larger cities
- **Unlocks**: Hydraulics, Urban Planning

### Advanced Economic Technologies

#### Banking
- **Science Cost**: 50 points
- **Research Time**: 8-12 turns
- **Prerequisites**: Currency, Mathematics
- **Effects**:
  - Interest accumulation on stored gold (+5% per turn)
  - Enables banks and financial institutions
  - +30% gold income from cities
  - Unlocks investment and credit systems
- **Unlocks**: Capitalism, Insurance

#### Industrialization
- **Science Cost**: 100 points
- **Research Time**: 15-20 turns
- **Prerequisites**: Steam Power, Economics
- **Effects**:
  - +100% production from all sources
  - Unlocks factories and mass production
  - -50% construction time for buildings
  - Enables industrial-scale resource processing
- **Unlocks**: Modern Economics, Assembly Line

## 10.4 Scientific Technology Branch

### Foundational Scientific Technologies

#### Writing
- **Science Cost**: 12 points
- **Research Time**: 3-4 turns
- **Prerequisites**: None
- **Effects**:
  - +15% science from all sources
  - Enables libraries and schools
  - Unlocks record-keeping and administration
  - Cultural and diplomatic benefits
- **Unlocks**: Literature, Mathematics, Law

#### Mathematics
- **Science Cost**: 20 points
- **Research Time**: 4-6 turns
- **Prerequisites**: Writing
- **Effects**:
  - +25% science from all sources
  - -15% technology research time
  - Enables universities and advanced learning
  - Unlocks engineering calculations
- **Unlocks**: Engineering, Astronomy, Philosophy

#### Philosophy
- **Science Cost**: 25 points
- **Research Time**: 5-7 turns
- **Prerequisites**: Writing
- **Effects**:
  - +20% influence generation
  - Enables great thinkers and cultural advancement
  - Improves diplomatic relations (+10% to all)
  - Unlocks ethical and moral systems
- **Unlocks**: Ethics, Political Theory, Scientific Method

### Advanced Scientific Technologies

#### Scientific Method
- **Science Cost**: 60 points
- **Research Time**: 10-14 turns
- **Prerequisites**: Philosophy, Mathematics
- **Effects**:
  - +50% science from all sources
  - -25% research time for all technologies
  - Enables experimental research and innovation
  - Unlocks peer review and scientific collaboration
- **Unlocks**: Modern Physics, Chemistry, Biology

#### Astronomy
- **Science Cost**: 45 points
- **Research Time**: 8-11 turns
- **Prerequisites**: Mathematics
- **Effects**:
  - +3 vision range for all cities
  - Enables navigation improvements
  - +20% science from observatories
  - Unlocks calendar and seasonal planning
- **Unlocks**: Navigation, Optics, Space Theory

#### Optics
- **Science Cost**: 40 points
- **Research Time**: 7-10 turns
- **Prerequisites**: Astronomy
- **Effects**:
  - +1 vision range for all units
  - +2 range for archer units
  - Enables telescopes and microscopes
  - Improves scientific observation capabilities
- **Unlocks**: Chemistry, Modern Physics

### Revolutionary Scientific Technologies

#### Electricity
- **Science Cost**: 120 points
- **Research Time**: 18-25 turns
- **Prerequisites**: Physics, Chemistry
- **Effects**:
  - Unlocks electrical power and lighting
  - +200% science from universities
  - Enables telegraph and communication
  - Revolutionary industrial applications
- **Unlocks**: Radio, Computers, Modern Technology

## 10.5 Cultural Technology Branch

### Early Cultural Technologies

#### Mysticism
- **Science Cost**: 10 points
- **Research Time**: 2-3 turns
- **Prerequisites**: None
- **Effects**:
  - +20% influence from temples
  - Enables religious buildings and practices
  - Improves population happiness
  - Unlocks spiritual and cultural development
- **Unlocks**: Religion, Meditation, Ceremonial Burial

#### Art
- **Science Cost**: 15 points
- **Research Time**: 3-4 turns
- **Prerequisites**: None
- **Effects**:
  - +15% influence from all cultural buildings
  - Enables artists and cultural works
  - Improves city aesthetics and morale
  - Unlocks creative expression and beauty
- **Unlocks**: Literature, Music, Architecture

#### Ceremonial Burial
- **Science Cost**: 12 points
- **Research Time**: 2-4 turns
- **Prerequisites**: Mysticism
- **Effects**:
  - +10% influence per turn
  - Improved cultural memory and traditions
  - +20% unit morale when defending home territory
  - Unlocks ancestor worship and cultural continuity
- **Unlocks**: Religion, Philosophy

### Intermediate Cultural Technologies

#### Religion
- **Science Cost**: 30 points
- **Research Time**: 5-8 turns
- **Prerequisites**: Mysticism, Ceremonial Burial
- **Effects**:
  - +50% influence from temples
  - Enables organized religion and priesthood
  - +25% cultural defense against enemy influence
  - Unlocks missionary activities and conversion
- **Unlocks**: Theology, Monotheism, Religious Orders

#### Literature
- **Science Cost**: 35 points
- **Research Time**: 6-9 turns
- **Prerequisites**: Writing, Art
- **Effects**:
  - +30% influence from libraries
  - Enables epic works and cultural preservation
  - +15% science from cultural exchange
  - Unlocks storytelling and cultural identity
- **Unlocks**: Drama, Printing Press, Education

#### Music
- **Science Cost**: 25 points
- **Research Time**: 4-6 turns
- **Prerequisites**: Art
- **Effects**:
  - +20% influence from all buildings
  - Improves unit morale and coordination
  - +10% diplomatic success rates
  - Unlocks entertainment and cultural expression
- **Unlocks**: Drama, Instruments, Cultural Exchange

### Advanced Cultural Technologies

#### Political Theory
- **Science Cost**: 55 points
- **Research Time**: 9-13 turns
- **Prerequisites**: Philosophy, Law
- **Effects**:
  - Unlocks advanced government types
  - +40% influence from political buildings
  - Improved diplomatic capabilities
  - Enables political reforms and representation
- **Unlocks**: Democracy, Nationalism, International Law

#### Nationalism
- **Science Cost**: 70 points
- **Research Time**: 12-16 turns
- **Prerequisites**: Political Theory, Literature
- **Effects**:
  - +100% cultural defense
  - +25% unit effectiveness in home territory
  - Enables national identity and unity
  - +50% influence generation during wars
- **Unlocks**: Modern Diplomacy, Cultural Identity

## 10.6 Technology Synergies and Cross-Branch Effects

### Military-Economic Synergies

#### Bronze Working + Mining
- **Combined Effect**: Unlocks bronze tools and weapons
- **Bonus**: +15% production and +10% military unit effectiveness

#### Engineering + Currency
- **Combined Effect**: Enables advanced commercial infrastructure
- **Bonus**: -25% building costs, +20% trade route income

### Scientific-Cultural Synergies

#### Mathematics + Philosophy
- **Combined Effect**: Unlocks logical reasoning and scientific inquiry
- **Bonus**: +30% science generation, +20% diplomatic success

#### Writing + Art
- **Combined Effect**: Enables written cultural works and preservation
- **Bonus**: +25% influence from cultural buildings

### Economic-Cultural Synergies

#### Currency + Religion
- **Combined Effect**: Unlocks religious economics and tithing
- **Bonus**: +15% gold income, +20% influence from temples

## 10.7 Technology Research Strategy

### Research Efficiency Calculation

```javascript
class TechnologyResearch {
    calculateResearchTime(technology, player) {
        const baseCost = technology.scienceCost;
        const sciencePerTurn = player.getScienceIncome();
        const researchBonuses = player.getResearchBonuses();
        
        // Apply bonuses
        const modifiedCost = baseCost * (1 - researchBonuses.timeReduction);
        const turnsRequired = Math.ceil(modifiedCost / sciencePerTurn);
        
        return Math.max(turnsRequired, technology.minimumTurns || 1);
    }
    
    getResearchRecommendations(player, gameState) {
        const availableTechs = this.getAvailableTechnologies(player);
        const recommendations = [];
        
        for (let tech of availableTechs) {
            const priority = this.calculatePriority(tech, player, gameState);
            const efficiency = this.calculateEfficiency(tech, player);
            
            recommendations.push({
                technology: tech,
                priority: priority,
                efficiency: efficiency,
                estimatedTurns: this.calculateResearchTime(tech, player)
            });
        }
        
        return recommendations.sort((a, b) => b.priority - a.priority);
    }
}
```

### Optimal Research Paths

#### Military Focus Path
1. **Bronze Working** → Iron Working → Engineering → Gunpowder
2. **Archery** → Engineering → Siege Warfare
3. **Animal Husbandry** → Military Logistics → Professional Army

#### Economic Focus Path
1. **Agriculture** → Currency → Banking → Industrialization
2. **Mining** → Metallurgy → Advanced Manufacturing
3. **Pottery** → Trade Networks → Mercantilism

#### Scientific Focus Path
1. **Writing** → Mathematics → Scientific Method → Modern Physics
2. **Philosophy** → Ethics → Research Methodology
3. **Astronomy** → Optics → Advanced Observation

#### Cultural Focus Path
1. **Art** → Literature → Cultural Heritage → National Identity
2. **Mysticism** → Religion → Theology → Spiritual Leadership
3. **Music** → Drama → Cultural Exchange → International Relations

---

## [GO BACK](0_CONTENT.md)