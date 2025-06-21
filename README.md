# ⚡ Web Turn-Based Strategy

A modern full-stack web turn-based strategy with **Laravel 11**, **Vue 3**, **Docker**, and **WebSockets** — everything preconfigured to get you started fast.

## 🏗️ Tech Stack

| Layer              | Technology                                  |
| ------------------ | ------------------------------------------- |
| **Backend**        | Laravel 11 (PHP 8.3), Laravel Reverb        |
| **Frontend**       | Vue 3, TypeScript, Vite, TailwindCSS        |
| **Database**       | PostgreSQL 15 with JSONB                    |
| **Cache**          | Redis (queues, pub/sub)                     |
| **Web Server**     | Nginx                                       |
| **Infrastructure** | Docker, Makefile                            |
| **Mail**           | Mailpit                                     |

## 📁 Project Structure

```
.
├── backend/                 # Laravel 11 application
├── frontend/                # Vue 3 + TypeScript app
├── infra/                   # Docker-based infrastructure
│   ├── docker/              # Dockerfiles
│   ├── nginx/               # Nginx config
│   └── docker-compose.yml   # Service definitions
├── docs/                    # Project documentation
├── Makefile                 # Dev commands
└── README.md
```

## 🚀 Getting Started

### ✅ Requirements

* Docker + Docker Compose
* GNU Make (optional, for command shortcuts)

### 🧰 Setup

```bash
make env       # Copy .env.example → .env for all services
make build     # Build containers
make dev       # Start containers
make install   # Install dependencies
make migrate   # Run DB migrations
make reverb    # Start WebSocket server
make queue     # Start Laravel Queue worker
```

Access the app:

* Frontend: [http://localhost:8000](http://localhost:8000)
* API Ping: [http://localhost:8000/api/ping](http://localhost:8000/api/ping)
* Mail Web Interface: [http://localhost:8025](http://localhost:8025)

## 🛠️ Make Commands

### Development

| Command      | Description                   |
| ------------ | ----------------------------- |
| `make env`   | Copy `.env.example` to `.env` |
| `make dev`   | Start all Docker services     |
| `make down`  | Stop all containers           |
| `make build` | Rebuild all containers        |
| `make logs`  | Tail logs from all services   |

### Application

| Command        | Description                    |
| -------------- | ------------------------------ |
| `make install` | Install Composer dependencies  |
| `make migrate` | Run DB migrations              |
| `make reverb`  | Start WebSocket server         |
| `make queue`   | Start queue worker             |
| `make test`    | Test WebSocket event broadcast |

## 🌐 API Endpoints

| Method | Endpoint    | Description  |
| ------ | ----------- | ------------ |
| GET    | `/api/ping` | Health check |

## 🐳 Docker Services

| Service    | Description                              | Port |
| ---------- | ---------------------------------------- | ---- |
| `frontend` | Vue + Vite                               | N/A  |
| `backend`  | Laravel 11 + Reverb WebSocket            | N/A  |
| `postgres` | PostgreSQL 15 with JSONB support         | 5432 |
| `redis`    | Redis for queues and pub/sub             | 6379 |
| `nginx`    | Reverse proxy for frontend/backend       | 8000 |
| `mailpit`  | Local Mail SMTP Server                   | 8025/1025 |

## ❗ Troubleshooting

### 🧩 Frontend: 502 Bad Gateway

* Is the frontend running? → `docker compose -f infra/docker-compose.yml ps`
* View logs: → `docker compose -f infra/docker-compose.yml logs frontend`

### 🐘 Backend Errors

* Check backend logs: `docker compose -f infra/docker-compose.yml logs backend`
* Ensure DB is up and migrations are applied: `make migrate`

### 🔌 WebSocket Issues

* Is the Reverb server running? → `make reverb`
* Check Redis status: `docker compose -f infra/docker-compose.yml logs redis`

## 🤝 Contributing

1. Fork this repo
2. Create a feature branch
3. Make your changes
4. Test via `make dev`
5. Open a pull request 🚀

## 📄 License

This project is licensed under the [MIT License](LICENSE).
