# Section 11: Resources and Economic System

## 11.1 Resource Framework Overview

The economic system forms the backbone of strategic decision-making in the game. Four primary resources drive all player actions: **Gold**, **Production**, **Science**, and **Influence**. Each resource serves specific purposes and requires different approaches to maximize efficiency.

### Economic Philosophy
- **Resource Scarcity**: Limited resources force meaningful choices
- **Opportunity Cost**: Investing in one area reduces capacity in others
- **Economic Cycles**: Resource generation and consumption create strategic rhythm
- **Trade Dependencies**: Cooperation and competition through resource exchange

## 11.2 Primary Resource Types

### Gold 💰 (Economic Resource)

**Primary Functions**:
- Unit recruitment and maintenance
- Emergency resource conversion
- Diplomatic incentives and bribes
- Trade and commerce activities
- Rush production capabilities

**Generation Sources**:
```javascript
const goldSources = {
    cities: {
        base: 2, // per city per turn
        population: 0.1, // per population point
        buildings: {
            farm: 2,
            market: 3,
            bank: 5,
            tradePost: 1 // per trade route
        }
    },
    tradeRoutes: {
        domestic: 1,
        international: 2,
        merchant: 3 // with merchant units
    },
    taxation: {
        lowTax: 0.8, // 80% of base income
        standardTax: 1.0, // 100% of base income
        highTax: 1.3 // 130% income, -10% happiness
    }
};
```

**Advanced Gold Mechanics**:
- **Interest**: Banks provide 5% interest on stored gold
- **Inflation**: Large gold stockpiles may cause inflation penalties
- **Emergency Conversion**: Convert other resources to gold at 3:1 ratio
- **Trade Fluctuation**: Resource prices vary based on supply and demand

### Production 🔨 (Industrial Resource)

**Primary Functions**:
- Building construction and upgrades
- Infrastructure development
- Unit equipment and improvements
- Wonder construction
- Defensive fortifications

**Generation Sources**:
```javascript
const productionSources = {
    cities: {
        base: 1, // per city per turn
        buildings: {
            mine: 3,
            workshop: 2,
            factory: 5, // late game
            quarry: 2 // on stone deposits
        }
    },
    terrain: {
        hills: 1, // bonus from hills
        forest: 1, // with proper technology
        mountains: 2 // with advanced mining
    },
    specialists: {
        engineer: 2,
        craftsman: 1,
        foreman: 3 // late game
    }
};
```

**Production Efficiency**:
- **Industrial Capacity**: Maximum production per turn based on infrastructure
- **Waste Factor**: Inefficient production at high output levels
- **Stockpiling**: Production can be stored for large projects
- **Rush Construction**: Use gold to accelerate production

### Science 🔬 (Research Resource)

**Primary Functions**:
- Technology research and development
- Innovation and discovery
- Academic institutions maintenance
- Research cooperation with other players
- Scientific wonder construction

**Generation Sources**:
```javascript
const scienceSources = {
    cities: {
        base: 0, // no base science generation
        buildings: {
            library: 2,
            university: 4,
            observatory: 3,
            laboratory: 6 // late game
        }
    },
    specialists: {
        scientist: 3,
        philosopher: 2,
        researcher: 4 // late game
    },
    cooperation: {
        culturalExchange: 0.15, // +15% with cultural agreement
        researchPact: 0.25, // +25% with research cooperation
        alliance: 0.10 // +10% with military alliance
    }
};
```

**Research Mechanics**:
- **Research Overflow**: Excess science applies to next technology
- **Research Cooperation**: Share science with allies
- **Scientific Breakthroughs**: Random science bonuses from high investment
- **Brain Drain**: Lose science when cities are captured

### Influence 🎭 (Political Resource)

**Primary Functions**:
- Diplomatic actions and agreements
- Cultural expansion and conversion
- City-state relations
- Wonder construction (cultural wonders)
- Victory condition achievement

**Generation Sources**:
```javascript
const influenceSources = {
    cities: {
        base: 0, // no base influence generation
        buildings: {
            temple: 2,
            theater: 3,
            monument: 2,
            palace: 5,
            culturalCenter: 4 // late game
        }
    },
    wonders: {
        greatLibrary: 3,
        hanging_gardens: 2,
        colosseum: 4
    },
    policies: {
        culturalTraditions: 1,
        artisticExpression: 2,
        diplomaticCorps: 1
    }
};
```

**Influence Mechanics**:
- **Cultural Pressure**: Influence spreads to neighboring cities
- **Diplomatic Currency**: Spend influence for diplomatic bonuses
- **Cultural Defense**: High influence protects against enemy cultural conversion
- **Great People**: Influence generates great artists, diplomats, and prophets

## 11.3 Resource Generation and Management

### Base Income Calculation

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
            income.gold += this.calculateCityGold(city);
            income.production += this.calculateCityProduction(city);
            income.science += this.calculateCityScience(city);
            income.influence += this.calculateCityInfluence(city);
        });
        
        // Apply modifiers
        income.gold *= player.getGoldModifier();
        income.production *= player.getProductionModifier();
        income.science *= player.getScienceModifier();
        income.influence *= player.getInfluenceModifier();
        
        // Trade routes
        income.gold += player.getTradeRouteIncome();
        income.influence += player.getCulturalTradeIncome();
        
        return income;
    }
    
    calculateCityGold(city) {
        let gold = 2; // base income
        
        // Building bonuses
        gold += city.getBuilding('FARM') ? 2 : 0;
        gold += city.getBuilding('MARKET') ? 3 : 0;
        gold += city.getBuilding('BANK') ? 5 : 0;
        
        // Population bonus
        gold += city.population * 0.1;
        
        // Technology bonuses
        if (city.owner.hasTechnology('CURRENCY')) {
            gold *= 1.25;
        }
        
        return Math.floor(gold);
    }
}
```

### Resource Efficiency and Optimization

#### Diminishing Returns
Large resource stockpiles become less efficient:

```javascript
const efficiencyRates = {
    gold: {
        0: 1.0,      // 0-100 gold: 100% efficiency
        100: 0.95,   // 100-500 gold: 95% efficiency
        500: 0.90,   // 500-1000 gold: 90% efficiency
        1000: 0.85   // 1000+ gold: 85% efficiency
    },
    production: {
        0: 1.0,      // 0-50 production: 100% efficiency
        50: 0.95,    // 50-200 production: 95% efficiency
        200: 0.90    // 200+ production: 90% efficiency
    }
};
```

#### Specialization Bonuses
Cities can specialize in specific resource production:

| Specialization | Primary Bonus | Secondary Effect | Requirements |
|----------------|---------------|------------------|--------------|
| **Commercial Hub** | +50% gold | +20% trade route capacity | Market + 3 trade routes |
| **Industrial Center** | +50% production | -25% construction time | Workshop + Mine |
| **Research City** | +50% science | +15% technology speed | University + Library |
| **Cultural Capital** | +50% influence | +25% diplomatic success | Theater + Monument |

## 11.4 Trade and Commerce System

### Trade Route Mechanics

#### Trade Route Types
- **Domestic Routes**: Between your own cities (+1 gold each)
- **International Routes**: With other players (+2 gold, +1 influence)
- **Merchant Routes**: Using merchant units (+3 gold, requires protection)

#### Trade Route Establishment
```javascript
class TradeRouteManager {
    establishTradeRoute(cityA, cityB, type) {
        const route = {
            origin: cityA,
            destination: cityB,
            type: type,
            income: this.calculateTradeIncome(cityA, cityB, type),
            distance: this.calculateDistance(cityA, cityB),
            securityRisk: this.assessSecurityRisk(cityA, cityB)
        };
        
        if (this.validateTradeRoute(route)) {
            this.activeTradeRoutes.push(route);
            this.updateCityIncomes();
            return route;
        }
        
        return null;
    }
    
    calculateTradeIncome(cityA, cityB, type) {
        let baseIncome = type === 'DOMESTIC' ? 1 : 2;
        
        // Distance bonus (longer routes = more income)
        const distance = this.calculateDistance(cityA, cityB);
        const distanceBonus = Math.floor(distance / 5);
        
        // Technology bonuses
        const techBonus = this.getTechnologyBonuses(cityA.owner);
        
        return baseIncome + distanceBonus + techBonus;
    }
}
```

### Market Dynamics

#### Resource Pricing
Resource exchange rates fluctuate based on supply and demand:

```javascript
class MarketSystem {
    constructor() {
        this.baseExchangeRates = {
            goldToProduction: 2.0,  // 2 gold = 1 production
            goldToScience: 3.0,     // 3 gold = 1 science
            goldToInfluence: 2.5,   // 2.5 gold = 1 influence
            productionToScience: 1.5,
            productionToInfluence: 1.2
        };
        this.marketModifiers = new Map();
    }
    
    updateMarketPrices(gameState) {
        const supplyDemand = this.analyzeSupplyDemand(gameState);
        
        for (let [resource, data] of supplyDemand) {
            const modifier = this.calculatePriceModifier(data);
            this.marketModifiers.set(resource, modifier);
        }
    }
    
    calculateExchangeRate(fromResource, toResource) {
        const baseRate = this.baseExchangeRates[`${fromResource}To${toResource}`];
        const fromModifier = this.marketModifiers.get(fromResource) || 1.0;
        const toModifier = this.marketModifiers.get(toResource) || 1.0;
        
        return baseRate * (fromModifier / toModifier);
    }
}
```

## 11.5 Economic Strategies and Optimization

### Economic Victory Strategy

#### Resource Accumulation Path
1. **Early Game**: Focus on gold-generating buildings (farms, markets)
2. **Mid Game**: Establish international trade routes
3. **Late Game**: Control 50% of trade routes, accumulate 1000 gold

#### Trade Route Control
```javascript
class EconomicVictoryTracker {
    assessTradeControl(player, gameState) {
        const allRoutes = gameState.getAllTradeRoutes();
        const playerControlledRoutes = allRoutes.filter(route => 
            this.isControlledByPlayer(route, player)
        );
        
        const controlPercentage = playerControlledRoutes.length / allRoutes.length;
        
        return {
            totalRoutes: allRoutes.length,
            controlledRoutes: playerControlledRoutes.length,
            controlPercentage: controlPercentage,
            requirementMet: controlPercentage >= 0.50
        };
    }
    
    isControlledByPlayer(route, player) {
        // Player controls route if they own both cities or have strong influence
        return (route.origin.owner === player && route.destination.owner === player) ||
               (this.hasTradeInfluence(route, player));
    }
}
```

### Resource Management Best Practices

#### Balanced Development
- **25% Gold**: Military units and emergency needs
- **35% Production**: Infrastructure and buildings
- **25% Science**: Technology advancement
- **15% Influence**: Diplomacy and cultural development

#### Crisis Management
Emergency resource allocation during wartime:
- **Wartime Economy**: 50% production, 30% gold, 15% science, 5% influence
- **Recovery Phase**: 30% production, 25% gold, 30% science, 15% influence

## 11.6 Advanced Economic Features

### Banking and Financial Systems

#### Interest and Investment
```javascript
class BankingSystem {
    calculateInterest(player) {
        const goldStored = player.getStoredGold();
        const bankBuildings = player.getBankBuildings();
        
        let interestRate = 0;
        
        // Base interest from banks
        interestRate += bankBuildings.length * 0.02; // 2% per bank
        
        // Technology bonuses
        if (player.hasTechnology('BANKING')) {
            interestRate += 0.03; // +3% from banking technology
        }
        
        // Calculate interest with diminishing returns
        const maxInterest = goldStored * 0.10; // Maximum 10% interest
        const calculatedInterest = goldStored * interestRate;
        
        return Math.min(calculatedInterest, maxInterest);
    }
    
    processLoan(borrower, lender, amount, terms) {
        const loan = {
            borrower: borrower,
            lender: lender,
            principal: amount,
            interestRate: terms.rate,
            duration: terms.turns,
            collateral: terms.collateral,
            status: 'ACTIVE'
        };
        
        this.transferGold(lender, borrower, amount);
        this.activateLoans.push(loan);
        
        return loan;
    }
}
```

### Economic Warfare

#### Sanctions and Embargos
- **Trade Sanctions**: Reduce enemy trade route income by 50%
- **Resource Embargos**: Prevent specific resource trades
- **Economic Isolation**: Coordinate with allies to exclude target player

#### Market Manipulation
- **Resource Flooding**: Oversupply markets to crash prices
- **Artificial Scarcity**: Restrict supply to inflate prices
- **Currency Devaluation**: Coordinated selling to reduce enemy gold value

## 11.7 Economic Balance and Feedback Mechanisms

### Anti-Monopoly Measures

#### Progressive Taxation
Large economies face increased maintenance costs:

```javascript
const maintenanceScaling = {
    cities: {
        1: 0,      // First city: no maintenance
        2: 1,      // Second city: 1 gold
        3: 2,      // Third city: 2 gold
        4: 4,      // Fourth city: 4 gold (exponential scaling)
        5: 7       // Fifth city: 7 gold
    },
    tradeRoutes: {
        1: 0,      // First route: free
        2: 0,      // Second route: free
        3: 1,      // Third route: 1 gold maintenance
        4: 2       // Each additional: +1 gold
    }
};
```

#### Catch-Up Mechanisms
Players behind in economic development receive bonuses:

- **Development Aid**: +15% resource generation if below average
- **Trade Incentives**: Other players get bonuses for trading with struggling economies
- **Technology Transfer**: Reduced research costs for players behind in tech

---

## [GO BACK](0_CONTENT.md)