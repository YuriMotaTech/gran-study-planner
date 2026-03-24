up:
	docker compose up --build

down:
	docker compose down

test:
	docker compose run --rm backend vendor/bin/phpunit && docker compose run --rm frontend npm run test -- --run

migrate:
	docker compose run --rm backend php bin/migrate.php
