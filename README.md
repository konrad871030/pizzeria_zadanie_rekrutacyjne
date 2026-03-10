# Pizzeria QueueMaster (Symfony + Docker)

Aplikacja realizuje zadanie rekrutacyjne:
- moduł frontendowy do składania zamówień,
- moduł backendowy (worker) do realizacji 1 zamówienia co 10 minut,
- logowanie ostrzeżenia przy długiej kolejce.

## Stack

- Symfony 6 (minimalny kernel + kontrolery + komendy CLI)
- PHP 8.3 (kontenery Docker)
- MySQL 8.4
- Nginx
- Bootstrap (CDN)

## Uruchomienie

1. Uruchom kontenery:

```bash
docker compose up -d --build
```

Jeśli Docker Desktop ma mało zasobów lub build przerywa się błędami warstwy, użyj trybu sekwencyjnego:

```bash
$env:COMPOSE_PARALLEL_LIMIT=1
docker compose up -d --build
```

2. Wykonaj migrację:

```bash
docker compose exec app php bin/console app:migrate
```

3. Załaduj menu (<10 pozycji):

```bash
docker compose exec app php bin/console app:fixtures:menu
```

4. Otwórz aplikację:

[`http://localhost:8080`](http://localhost:8080)

## Co robi aplikacja

- Frontend:
  - pokazuje menu,
  - przyjmuje zamówienie (`pizza`, `ilość`, `email`, `adres`),
  - stale odświeża licznik kolejki i ETA przez polling endpointu `/api/queue/stats`.
- Backend:
  - komenda `app:process-next-order` oznacza najstarsze `pending` jako `delivered`,
  - worker uruchamia tę komendę w trybie ciągłym (daemon w serwisie `worker`) co 10 minut.

## Logi i próg długiej kolejki

- Próg: `QUEUE_ALERT_THRESHOLD` (domyślnie `5`).
- Gdy `pending >= threshold`, zapisywany jest warning do:
  - `var/log/app.log` (aplikacja),
  - `var/log/worker.log` (wyjście cron/workera).

## Przydatne komendy

```bash
# ręczne przetworzenie jednego zamówienia
docker compose exec app php bin/console app:process-next-order

# testy
docker compose exec app php vendor/bin/phpunit
```

## Szybki scenariusz testowy

1. Złóż 2-3 zamówienia przez UI.
2. Sprawdź, że licznik i ETA rosną.
3. Uruchom ręcznie:
   - `docker compose exec app php bin/console app:process-next-order`
4. Odśwież UI i potwierdź, że licznik maleje.
5. Przy większej liczbie zamówień sprawdź ostrzeżenia w `var/log/app.log`.

## Troubleshooting Docker (Windows)

Jeżeli pojawia się `failed to register layer ... input/output error`:

1. Zrestartuj Docker Desktop.
2. Zwolnij miejsce na dysku (min. kilka GB wolnego miejsca).
3. Wyczyść nieużywane zasoby:

```bash
docker system prune -af
docker builder prune -af
```

4. Zbuduj ponownie:

```bash
docker compose build --no-cache
docker compose up -d
```
