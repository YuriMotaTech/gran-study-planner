# Gran Study Planner

Full-stack study planner built with **PHP 8**, **React**, **TypeScript**, **MySQL**, and **Redis**, designed with **Hexagonal Architecture** and **TDD**.

## Why this project
This repository simulates a real-world educational planning product and demonstrates:
- backend design with ports/adapters and clear boundaries,
- REST APIs with cron automation and caching,
- frontend state management and component testing,
- incremental delivery with roadmap and CI.

## Tech stack
- Backend: PHP 8.3, PHPUnit
- Frontend: React 19, TypeScript, Vite, Vitest, RTL, Tailwind
- Infra: Docker Compose, MySQL 8, Redis 7

## Project structure
- `backend/` hexagonal backend
- `frontend/` React app
- `docs/architecture.md` architecture decisions
- `docs/roadmap.md` versioned delivery roadmap

## Quick start
1. Copy env file:
   - `cp .env.example .env`
2. Start stack:
   - `docker compose up --build`
3. Run migration:
   - `docker compose run --rm backend php bin/migrate.php`
4. Access:
   - API: `http://localhost:8080`
   - Frontend: `http://localhost:5173`

## Default login
- email: `candidate@gran.com`
- password: `gran123`

## API endpoints
- `POST /auth/login`
- `GET /health`
- `POST /study-plans`
- `GET /study-plans?status=&page=&perPage=&sortBy=&sortDirection=`
- `PATCH /study-plans/{id}`
- `DELETE /study-plans/{id}`
- `GET /dashboard`

## Testing
- Backend: `cd backend && composer install && vendor/bin/phpunit`
- Frontend: `cd frontend && npm install && npm run test -- --run`

## Cron
Run overdue marker manually:
- `cd backend && php bin/cron_mark_overdue.php`

(For production, schedule this command daily in crontab.)
