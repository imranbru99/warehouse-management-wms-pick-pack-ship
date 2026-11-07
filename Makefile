.PHONY: up down install migrate seed demo test lint analyse

up:
	docker compose up -d

down:
	docker compose down

install:
	composer install
	cp -n .env.example .env || true
	php artisan key:generate
	npm install
	npm run build

migrate:
	DRUTOGOTI_ENABLED=false php artisan migrate --force

seed:
	DRUTOGOTI_ENABLED=false php artisan db:seed --force

demo:
	DRUTOGOTI_ENABLED=false php artisan wms:demo

test:
	DRUTOGOTI_ENABLED=false php artisan test

lint:
	vendor/bin/pint

analyse:
	vendor/bin/phpstan analyse --memory-limit=2G
