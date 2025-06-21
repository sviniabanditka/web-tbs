.PHONY: dev logs migrate down build

dev:
	docker compose -f infra/docker-compose.yml up -d

logs:
	docker compose -f infra/docker-compose.yml logs -f

migrate:
	docker compose -f infra/docker-compose.yml exec backend php artisan migrate

down:
	docker compose -f infra/docker-compose.yml down

build:
	docker compose -f infra/docker-compose.yml build

queue:
	docker compose -f infra/docker-compose.yml exec backend php artisan queue:work

reverb:
	docker compose -f infra/docker-compose.yml exec backend php artisan reverb:start

test:
	docker compose -f infra/docker-compose.yml exec backend php artisan event:test 1 test test

install:
	docker compose -f infra/docker-compose.yml exec backend composer install
	docker compose -f infra/docker-compose.yml exec frontend npm install

env:
	cp infra/.env.example infra/.env
	cp backend/.env.example backend/.env
	cp frontend/.env.example frontend/.env