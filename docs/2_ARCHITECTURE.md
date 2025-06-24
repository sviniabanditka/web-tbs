# Section 2: Architecture and Technical Foundation

## 2.1 Overall System Architecture

The game is built on a modern three-tier architecture, including the presentation layer (Frontend), business logic layer (Backend API), and data layer (Database Layer). The server foundation is built on Laravel 10.x PHP framework, which provides a reliable base for game logic processing, session management, and API interactions.

### Technology Stack
- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Vue.js 3.x with Composition API
- **Styling**: TailwindCSS 3.x
- **Database**: PostgreSQL 15.x (primary), Redis 7.x (cache/sessions)
- **Real-time Communication**: WebSockets (Laravel Broadcasting)
- **Containerization**: Docker with Kubernetes orchestration
- **Message Queue**: Laravel Queues with Redis driver

The **client-side** is implemented in Vue.js 3.x using the Composition API, ensuring interface reactivity and efficient game state management. TailwindCSS 3.x is used to create a responsive and modern user interface supporting both dark and light themes.

## 2.2 Server Architecture

### Core Server Components

#### Game Engine Service
The central service for processing game logic, including:
- **Move Validation**: Ensuring all player actions comply with game rules
- **Combat Calculations**: Processing all battle interactions and damage calculations
- **Resource Generation**: Computing resource income and expenditure
- **State Management**: Maintaining consistent game state across all players
- **Event Processing**: Handling game events like technology research completion, building construction

#### Match Service
Game session management including:
- **Session Creation**: Initializing new games with proper starting conditions
- **Player Management**: Handling player connections, disconnections, and reconnections
- **Turn Management**: Coordinating synchronous turns and time limits
- **Game Persistence**: Saving and loading game states for marathon modes

#### Player Service
User management and authentication:
- **Authentication**: JWT-based authentication with refresh tokens
- **Authorization**: Role-based access control and permissions
- **Profile Management**: Player statistics, preferences, and achievements
- **Rating System**: ELO-based ranking with seasonal adjustments

#### Notification Service
Real-time notifications through WebSocket connections:
- **Game Events**: Turn changes, combat results, diplomatic messages
- **System Notifications**: Connection status, error messages, warnings
- **Social Features**: Friend requests, chat messages, tournament invitations

#### Analytics Service
Game metrics collection and analysis:
- **Performance Metrics**: Response times, server load, error rates
- **Game Balance Data**: Win rates by strategy, unit effectiveness, resource generation
- **Player Behavior**: Session duration, action patterns, drop-off points

### Architectural Patterns

#### Repository Pattern
Abstracts data access layer for testability and maintainability:
```php
interface GameRepositoryInterface
{
    public function findById(string $gameId): Game;
    public function save(Game $game): void;
    public function findActiveGames(): Collection;
}
```

#### Service Layer Pattern
Encapsulates business logic in reusable services:
```php
class GameEngine
{
    public function processPlayerAction(PlayerId $playerId, Action $action): GameResult;
    public function calculateCombat(Unit $attacker, Unit $defender): CombatResult;
    public function generateResources(Game $game): ResourceGeneration;
}
```

#### Observer Pattern
Handles game events and notifications:
```php
class GameEventDispatcher
{
    public function dispatch(GameEvent $event): void;
    // Notifies all registered listeners
}
```

#### Command Pattern
Processes game actions with undo/redo capability:
```php
interface GameCommand
{
    public function execute(Game $game): void;
    public function undo(Game $game): void;
}
```

## 2.3 Client Architecture

### Vue.js Application Structure

```
src/
├── components/
│   ├── Game/
│   │   ├── GameBoard.vue (hexagonal grid rendering)
│   │   ├── UnitPanel.vue (unit management interface)
│   │   ├── BuildingPanel.vue (building construction interface)
│   │   ├── ResourcePanel.vue (resource display and management)
│   │   └── TechTree.vue (technology research interface)
│   ├── UI/
│   │   ├── ActionBar.vue (action point management)
│   │   ├── ChatPanel.vue (player communication)
│   │   ├── DiplomacyPanel.vue (diplomatic interface)
│   │   └── NotificationCenter.vue (game notifications)
│   └── Common/
│       ├── Modal.vue (reusable modal component)
│       ├── Button.vue (standardized buttons)
│       └── LoadingSpinner.vue (loading indicators)
├── stores/ (Pinia State Management)
│   ├── gameStore.js (game state management)
│   ├── playerStore.js (player data and preferences)
│   ├── uiStore.js (interface state)
│   └── websocketStore.js (connection management)
├── services/
│   ├── apiService.js (HTTP client with interceptors)
│   ├── websocketService.js (WebSocket connection management)
│   ├── gameLogic.js (client-side validation and predictions)
│   └── audioService.js (sound effects and music)
├── utils/
│   ├── hexGrid.js (hexagonal grid mathematics)
│   ├── animations.js (game animations and transitions)
│   ├── constants.js (game constants and enums)
│   └── validators.js (input validation utilities)
└── assets/
    ├── images/ (game sprites and icons)
    ├── sounds/ (audio files)
    └── styles/ (custom CSS and themes)
```

### State Management with Pinia

```javascript
// gameStore.js
export const useGameStore = defineStore('game', {
  state: () => ({
    currentGame: null,
    players: [],
    currentTurn: 0,
    actionPoints: 8,
    selectedUnit: null,
    availableActions: []
  }),
  
  actions: {
    async executeAction(action) {
      // Optimistic update
      this.updateGameState(action);
      
      try {
        const result = await gameAPI.executeAction(action);
        this.confirmAction(result);
      } catch (error) {
        this.revertAction(action);
        throw error;
      }
    }
  }
});
```

## 2.4 Network Architecture and Communication

### WebSocket Implementation
Real-time game interactions using Laravel Broadcasting:

```php
// Server-side event broadcasting
broadcast(new GameActionEvent($gameId, $action))->toOthers();

// Client-side event handling
Echo.join(`game.${gameId}`)
    .listen('GameActionEvent', (event) => {
        gameStore.handleRemoteAction(event.action);
    });
```

### Message Protocol
Standardized message format for all game communications:

```typescript
interface GameMessage {
  type: 'GAME_ACTION' | 'GAME_STATE' | 'PLAYER_EVENT' | 'SYSTEM_MESSAGE';
  gameId: string;
  playerId: string;
  timestamp: string;
  data: ActionData | StateData | EventData | SystemData;
}

interface ActionData {
  actionType: 'MOVE_UNIT' | 'ATTACK' | 'BUILD' | 'RESEARCH' | 'DIPLOMACY';
  parameters: Record<string, any>;
  actionPoints: number;
}
```

### API Endpoints
RESTful API design for non-real-time operations:

```php
// Game management routes
Route::group(['prefix' => 'api/v1'], function () {
    Route::post('/games', [GameController::class, 'create']);
    Route::get('/games/{gameId}', [GameController::class, 'show']);
    Route::post('/games/{gameId}/join', [GameController::class, 'join']);
    Route::delete('/games/{gameId}/leave', [GameController::class, 'leave']);
});

// Player management routes
Route::group(['prefix' => 'api/v1/players'], function () {
    Route::get('/profile', [PlayerController::class, 'profile']);
    Route::put('/profile', [PlayerController::class, 'updateProfile']);
    Route::get('/stats', [PlayerController::class, 'statistics']);
});
```

## 2.5 Database Design and Data Storage

### Primary Database (PostgreSQL)
Structured data storage with ACID compliance:

```sql
-- Core game tables
CREATE TABLE games (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(100) NOT NULL,
    status game_status_enum NOT NULL DEFAULT 'waiting',
    max_players INTEGER NOT NULL CHECK (max_players BETWEEN 2 AND 8),
    current_turn INTEGER NOT NULL DEFAULT 1,
    turn_timer INTEGER NOT NULL DEFAULT 300, -- 5 minutes in seconds
    map_seed BIGINT NOT NULL,
    victory_conditions JSONB NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Player participation in games
CREATE TABLE game_players (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    game_id UUID NOT NULL REFERENCES games(id) ON DELETE CASCADE,
    player_id UUID NOT NULL REFERENCES players(id),
    faction VARCHAR(50) NOT NULL,
    turn_order INTEGER NOT NULL,
    is_alive BOOLEAN NOT NULL DEFAULT TRUE,
    action_points INTEGER NOT NULL DEFAULT 8,
    resources JSONB NOT NULL DEFAULT '{"gold": 10, "production": 5, "science": 0, "influence": 0}',
    UNIQUE(game_id, player_id),
    UNIQUE(game_id, turn_order)
);

-- Game state snapshots for persistence
CREATE TABLE game_states (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    game_id UUID NOT NULL REFERENCES games(id) ON DELETE CASCADE,
    turn_number INTEGER NOT NULL,
    state_data JSONB NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(game_id, turn_number)
);
```

### Redis Cache Layer
High-performance caching for active game sessions:

```php
// Active game session caching
Cache::put("game:{$gameId}:state", $gameState, 3600);
Cache::put("game:{$gameId}:players", $activePlayers, 3600);

// Player session management
Cache::put("player:{$playerId}:session", $sessionData, 7200);

// Leaderboards and rankings
Cache::put("leaderboard:global", $rankings, 1800);
```

## 2.6 Security Architecture

### Authentication and Authorization
Multi-layered security approach:

```php
// JWT token authentication
class JWTAuthService
{
    public function generateToken(Player $player): string;
    public function validateToken(string $token): ?Player;
    public function refreshToken(string $refreshToken): string;
}

// Role-based permissions
class GamePermissions
{
    public function canExecuteAction(Player $player, Game $game, Action $action): bool;
    public function canViewGameState(Player $player, Game $game): bool;
}
```

### Anti-Cheat Measures
Comprehensive cheat prevention:

```php
class AntiCheatValidator
{
    public function validateAction(Player $player, Game $game, Action $action): ValidationResult;
    public function detectAnomalousPatterns(Player $player): array;
    public function verifyGameStateIntegrity(Game $game): bool;
}
```

### Rate Limiting and DDoS Protection
Request limiting and abuse prevention:

```php
// API rate limiting
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// WebSocket connection limiting
RateLimiter::for('websocket', function (Request $request) {
    return Limit::perMinute(30)->by($request->ip());
});
```

## 2.7 Performance and Scalability

### Horizontal Scaling Strategy
Microservices architecture with independent scaling:

```yaml
# Kubernetes deployment example
apiVersion: apps/v1
kind: Deployment
metadata:
  name: game-engine
spec:
  replicas: 3
  selector:
    matchLabels:
      app: game-engine
  template:
    spec:
      containers:
      - name: game-engine
        image: game/engine:latest
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"
```

### Database Optimization
Query optimization and indexing strategy:

```sql
-- Performance indexes
CREATE INDEX idx_games_status_created ON games(status, created_at);
CREATE INDEX idx_game_players_game_turn ON game_players(game_id, turn_order);
CREATE INDEX idx_game_states_game_turn ON game_states(game_id, turn_number DESC);

-- Partitioning for large tables
CREATE TABLE game_actions_2025 PARTITION OF game_actions
FOR VALUES FROM ('2025-01-01') TO ('2026-01-01');
```

### Caching Strategy
Multi-level caching implementation:

```php
class GameCacheManager
{
    // L1: Application-level cache
    public function getGameState(string $gameId): ?GameState;
    
    // L2: Redis distributed cache
    public function getCachedData(string $key): mixed;
    
    // L3: Database query result cache
    public function getCachedQuery(string $query, array $params): Collection;
}
```

## 2.8 Monitoring and Observability

### Application Performance Monitoring
Comprehensive monitoring setup:

```php
// Custom metrics collection
class GameMetrics
{
    public function recordTurnDuration(string $gameId, float $duration): void;
    public function recordActionExecution(string $actionType, float $duration): void;
    public function recordPlayerCount(int $activeCount): void;
}

// Health check endpoints
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'redis' => Cache::store('redis')->get('health_check') ? 'connected' : 'disconnected',
        'timestamp' => now()->toISOString()
    ]);
});
```

### Logging Strategy
Structured logging for debugging and analysis:

```php
Log::channel('game_actions')->info('Player action executed', [
    'game_id' => $gameId,
    'player_id' => $playerId,
    'action_type' => $action->getType(),
    'action_points_used' => $action->getActionPoints(),
    'game_turn' => $game->getCurrentTurn(),
    'execution_time_ms' => $executionTime
]);
```

---

## [GO BACK](0_CONTENT.md)