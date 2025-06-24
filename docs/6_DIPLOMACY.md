# Section 6: Diplomatic System and Reputation

## 6.1 Diplomacy Overview

The **Diplomatic System** enables players to interact on a political level, forming alliances, negotiating agreements, conducting trade, and influencing the game's outcome without direct military action. This system adds strategic depth and opens alternative victory paths beyond pure military conquest.

### Diplomatic Philosophy
- **Meaningful Choices**: Diplomatic decisions have lasting consequences
- **Trust and Betrayal**: Reputation system tracks player reliability
- **Multiple Strategies**: Diplomatic, economic, and military approaches are equally viable
- **Dynamic Relationships**: Player relationships evolve throughout the game

## 6.2 Diplomatic Actions and Agreements

### Available Diplomatic Actions

| Agreement Type | AP Cost | Duration | Effect | Requirements |
|----------------|---------|----------|--------|--------------|
| Non-Aggression Pact | 1 | 10 turns | Prevents attacks between parties | Neutral+ reputation |
| Trade Agreement | 1 | Permanent | +20% gold from trade routes | Commercial technology |
| Military Alliance | 2 | 15 turns | Mutual defense obligation | Friendly+ reputation |
| Cultural Exchange | 1 | Permanent | +10% science for both players | Writing technology |
| Resource Trade | 1 | 5 turns | Direct resource exchange | Active trade routes |
| Research Cooperation | 2 | 10 turns | Shared technology benefits | Advanced diplomacy |
| Joint Victory | 3 | Permanent | Shared victory conditions | Maximum reputation |

### Advanced Diplomatic Features

#### Secret Agreements
- **Hidden Treaties**: Agreements not visible to other players
- **Espionage Pacts**: Shared intelligence gathering
- **Conditional Alliances**: Agreements activated under specific circumstances

#### Diplomatic Leverage
- **Economic Sanctions**: Trade embargos and resource restrictions
- **Military Pressure**: Troop positioning for diplomatic advantage
- **Cultural Influence**: Using influence points to sway opinions

## 6.3 Reputation System

### Reputation Mechanics

**Reputation** represents the trust and diplomatic standing between any two players, ranging from -100 (mortal enemies) to +100 (trusted allies).

```javascript
class ReputationSystem {
    constructor() {
        this.reputationMatrix = new Map(); // player pairs -> reputation score
        this.reputationHistory = new Map(); // track reputation changes
    }
    
    getReputation(player1, player2) {
        const key = this.createPlayerPairKey(player1, player2);
        return this.reputationMatrix.get(key) || 0; // Default neutral
    }
    
    modifyReputation(player1, player2, change, reason) {
        const currentRep = this.getReputation(player1, player2);
        const newRep = Math.max(-100, Math.min(100, currentRep + change));
        
        this.setReputation(player1, player2, newRep);
        this.recordReputationChange(player1, player2, change, reason);
        
        return newRep;
    }
}
```

### Reputation Categories

| Reputation Range | Relationship Status | Diplomatic Modifiers |
|------------------|-------------------|---------------------|
| 80 to 100 | **Trusted Ally** | -50% AP costs, +25% trade benefits |
| 50 to 79 | **Close Friend** | -25% AP costs, +15% trade benefits |
| 20 to 49 | **Friendly** | Standard costs, +10% trade benefits |
| -19 to 19 | **Neutral** | Standard diplomatic costs |
| -49 to -20 | **Unfriendly** | +25% AP costs, trade restrictions |
| -79 to -50 | **Hostile** | +50% AP costs, limited agreements |
| -100 to -80 | **Mortal Enemy** | +100% AP costs, war declarations |

### Reputation Modifiers

#### Positive Reputation Changes
| Action | Reputation Gain | Conditions |
|--------|----------------|------------|
| Honoring Agreement | +10 to +20 | Fulfill treaty obligations |
| Military Assistance | +15 to +25 | Help ally in combat |
| Gift/Aid | +5 to +15 | Resource transfers |
| Joint Victory Share | +30 | Share victory with ally |
| Protecting Ally | +20 | Defend ally from aggression |

#### Negative Reputation Changes
| Action | Reputation Loss | Conditions |
|--------|----------------|------------|
| Breaking Agreement | -30 to -50 | Violate active treaties |
| Surprise Attack | -25 to -40 | Attack without war declaration |
| Trade Embargo | -10 to -20 | Economic warfare |
| Betrayal | -40 to -60 | Attack former ally |
| War Crimes | -20 to -35 | Attacking civilians/cities |

## 6.4 Diplomatic Interface and Communication

### Communication Channels

#### Formal Diplomacy
- **Official Proposals**: Structured diplomatic offers with clear terms
- **Treaty Negotiations**: Multi-step process for complex agreements
- **Diplomatic Notes**: Formal communications and declarations

#### Informal Communication
- **Private Messages**: Direct player-to-player communication
- **Public Announcements**: Statements visible to all players
- **Emotes and Gestures**: Quick diplomatic signals

### Diplomatic UI Components

```vue
<template>
  <div class="diplomacy-panel">
    <!-- Player List -->
    <div class="player-list">
      <div v-for="player in otherPlayers" :key="player.id" class="player-card">
        <PlayerAvatar :player="player" />
        <ReputationMeter :reputation="getReputation(player)" />
        <ActiveAgreements :agreements="getAgreements(player)" />
        <DiplomaticActions :available-actions="getAvailableActions(player)" />
      </div>
    </div>
    
    <!-- Proposal Creation -->
    <ProposalBuilder 
      v-if="showProposalBuilder"
      :target-player="selectedPlayer"
      @submit="submitProposal"
      @cancel="cancelProposal"
    />
    
    <!-- Active Negotiations -->
    <div class="active-negotiations">
      <NegotiationCard 
        v-for="negotiation in activeNegotiations"
        :key="negotiation.id"
        :negotiation="negotiation"
        @accept="acceptProposal"
        @reject="rejectProposal"
        @counter="counterProposal"
      />
    </div>
  </div>
</template>
```

## 6.5 Treaty and Agreement Mechanics

### Agreement Lifecycle

#### Proposal Phase
1. **Initiation**: Player proposes agreement using AP
2. **Terms Definition**: Specify duration, conditions, and obligations
3. **Transmission**: Proposal sent to target player
4. **Response Window**: Target has limited time to respond

#### Negotiation Phase
1. **Review**: Target player examines proposal terms
2. **Counter-Proposal**: Modify terms and send back (optional)
3. **Iteration**: Multiple rounds of negotiation possible
4. **Final Decision**: Accept, reject, or let expire

#### Active Phase
1. **Implementation**: Agreement becomes active and binding
2. **Monitoring**: System tracks compliance with terms
3. **Benefits**: Both parties receive agreed-upon benefits
4. **Violations**: System detects and penalizes breaches

#### Termination Phase
1. **Natural Expiry**: Agreement ends after specified duration
2. **Mutual Cancellation**: Both parties agree to end early
3. **Violation**: Breach of terms terminates agreement
4. **Force Majeure**: External events (player elimination) end agreement

### Complex Agreement Examples

#### Comprehensive Trade Pact
```javascript
const tradePact = {
    type: 'COMPREHENSIVE_TRADE',
    participants: [player1, player2],
    duration: 20, // turns
    terms: {
        tradeBonus: 0.25, // +25% trade income
        resourceExchange: {
            player1: { gives: 'science', receives: 'gold' },
            player2: { gives: 'gold', receives: 'science' }
        },
        exclusivePartnership: true, // no competing trade agreements
        militaryClause: 'DEFENSIVE_ONLY' // no attacks on trade routes
    },
    breakConditions: [
        'ATTACK_TRADE_ROUTE',
        'COMPETING_AGREEMENT',
        'REPUTATION_BELOW_NEUTRAL'
    ]
};
```

#### Military Alliance Treaty
```javascript
const militaryAlliance = {
    type: 'MILITARY_ALLIANCE',
    participants: [player1, player2],
    duration: 15, // turns
    terms: {
        mutualDefense: true, // auto-join defensive wars
        sharedIntelligence: true, // vision sharing
        coordinatedAttacks: false, // no offensive obligations
        unitSupport: {
            maxDistance: 10, // hexes from ally territory
            supportTypes: ['DEFENSIVE', 'EMERGENCY_RELIEF']
        }
    },
    triggerConditions: {
        defenseActivation: 'ALLY_UNDER_ATTACK',
        responseTime: 2 // turns to respond
    }
};
```

## 6.6 Diplomatic Victory Conditions

### Cultural Victory Path
Achieving victory through diplomatic and cultural means:

#### Requirements
- **500 Influence Points**: Accumulated through cultural buildings and diplomatic success
- **75% Alliance Rate**: Maintain positive relations with 75% of remaining players
- **Cultural Dominance**: Control 40% of cultural sites on the map
- **Diplomatic Stability**: No active wars for final 10 turns

#### Cultural Victory Mechanics
```javascript
class CulturalVictoryTracker {
    checkVictoryConditions(player) {
        const influence = player.getTotalInfluence();
        const allianceRate = this.calculateAllianceRate(player);
        const culturalDominance = this.calculateCulturalDominance(player);
        const peacefulTurns = this.getPeacefulTurnCount(player);
        
        return {
            influence: influence >= 500,
            alliances: allianceRate >= 0.75,
            cultural: culturalDominance >= 0.40,
            peaceful: peacefulTurns >= 10,
            achieved: influence >= 500 && allianceRate >= 0.75 && 
                     culturalDominance >= 0.40 && peacefulTurns >= 10
        };
    }
}
```

## 6.7 Advanced Diplomatic Strategies

### Diplomatic Manipulation

#### Coalition Building
- **Alliance Networks**: Create interconnected alliance systems
- **Threat Assessment**: Unite against dominant players
- **Power Balance**: Maintain equilibrium through shifting alliances

#### Information Warfare
- **Intelligence Trading**: Sell information about other players
- **Disinformation**: Spread false intelligence to create conflicts
- **Reputation Assassination**: Damage rivals' diplomatic standing

### Economic Diplomacy

#### Trade Warfare
- **Economic Blockades**: Coordinate trade restrictions
- **Resource Monopolies**: Control scarce resources for leverage
- **Financial Incentives**: Use wealth to buy diplomatic favor

#### Development Cooperation
- **Technology Sharing**: Accelerate mutual technological progress
- **Infrastructure Projects**: Joint construction of beneficial buildings
- **Resource Optimization**: Specialized production agreements

## 6.8 AI Diplomatic Behavior (Future Enhancement)

### AI Personality Archetypes

#### The Pacifist
- **Behavior**: Prefers diplomatic solutions, avoids conflict
- **Strategy**: Focus on cultural and economic victories
- **Relationships**: Maintains positive relations with most players

#### The Opportunist
- **Behavior**: Changes alliances based on advantage
- **Strategy**: Exploits conflicts between other players
- **Relationships**: Fluid alliances, reputation varies

#### The Warlord
- **Behavior**: Aggressive expansion, military-first approach
- **Strategy**: Use diplomacy to isolate targets before attack
- **Relationships**: Generally hostile, temporary truces only

#### The Merchant
- **Behavior**: Trade-focused, economically minded
- **Strategy**: Build wealth through commerce and trade agreements
- **Relationships**: Positive with trading partners, neutral with others

---

## [GO BACK](0_CONTENT.md)