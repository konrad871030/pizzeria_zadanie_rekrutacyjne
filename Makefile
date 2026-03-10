up:
	docker compose up -d --build

down:
	docker compose down

migrate:
	docker compose exec app php bin/console app:migrate

fixtures:
	docker compose exec app php bin/console app:fixtures:menu

test:
	docker compose exec app php vendor/bin/phpunit

worker-once:
	docker compose exec app php bin/console app:process-next-order
