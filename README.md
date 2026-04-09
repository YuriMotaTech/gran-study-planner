# Gran Study Planner

Full-stack study planner built with **PHP 8**, **React**, **TypeScript**, **MySQL**, and **Redis**, designed with **Hexagonal Architecture** and **TDD**.

## Why this project
This repository simulates a real-world educational planning product and demonstrates:
- backend design with ports/adapters and clear boundaries,
- REST APIs with cron automation and caching,
- frontend state management and component testing,
- incremental delivery with roadmap and CI.

## Use case (PT/EN)

### Caso de uso (PT-BR)
O Gran Study Planner é uma ferramenta simples de planejamento de estudos que ajuda estudantes a criar, acompanhar e gerenciar planos de estudo diários com **status** e **prazos**.

**Funcionalidades principais**
- Login básico (demo com credenciais fixas — veja `Default login`)
- Dashboard com visão geral: totais por status (pendente, em andamento, concluído, atrasado)
- CRUD de planos de estudo: criar plano com título e data limite, listar com filtros (status, ordenação, paginação), editar status, deletar
- Job automático de atraso: um cron marca planos vencidos como “atrasados” (veja `Cron`)

**Exemplo de uso (estudante)**
- Loga → vê no dashboard 3 pendentes, 2 em andamento, 0 atrasados
- Cria o plano “Direito Constitucional — art. 5º” com prazo para amanhã
- Marca como “em andamento” quando começa a estudar
- Se não concluir até amanhã, o sistema marca automaticamente como “atrasado”
- No dashboard, vê o progresso e prioriza o que está atrasado primeiro

**Como ajuda o usuário**
- Organização visual: o dashboard mostra exatamente o que precisa de atenção hoje
- Automação de status: não precisa lembrar de marcar planos vencidos
- Filtros inteligentes: foca só nos “atrasados” ou “pendentes”
- Progresso mensurável: vê quantos planos concluiu vs. total

É tipo um “Gerenciador de Estudos” simplificado, mas com arquitetura moderna (**hexagonal no PHP**, **React/TS no front**) para demonstrar domínio da stack.

### Use case (EN)
Gran Study Planner is a simple study planning tool that helps students create, track, and manage daily study plans with **statuses** and **deadlines**.

**Key features**
- Basic login (demo with fixed credentials — see `Default login`)
- Dashboard overview: totals by status (pending, in progress, done, overdue)
- Study plan CRUD: create plans with title/deadline, list with filters (status, sorting, pagination), update status, delete
- Automatic overdue marking: a daily cron job marks expired plans as “overdue” (see `Cron`)

**Example flow (student)**
- Logs in → sees 3 pending, 2 in progress, 0 overdue on the dashboard
- Creates “Constitutional Law — art. 5” with a deadline for tomorrow
- Marks it as “in progress” when starting the session
- If not completed by tomorrow, the system automatically marks it as “overdue”
- Uses the dashboard to track progress and prioritize overdue items first

**How it helps**
- Visual organization: the dashboard makes priorities obvious
- Status automation: no need to manually tag expired plans
- Smart filters: focus on “overdue” or “pending” only
- Measurable progress: completed vs. total at a glance

It’s a lightweight “Study Manager” concept, built with a modern stack (**hexagonal PHP backend**, **React/TypeScript frontend**) to demonstrate real-world architecture skills.

## Tech stack
- Backend: PHP 8.3, PHPUnit
- Frontend: React 19, TypeScript, Vite, Vitest, RTL, Tailwind
- Infra: Docker Compose, MySQL 8, Redis 7

## Project structure
- `backend/` hexagonal backend
- `frontend/` React app
- `docs/architecture.md` architecture decisions
- `docs/roadmap.md` versioned delivery roadmap
- `docs/contributing.md` Git workflow (feature branches, referências locais)

## Git workflow
Preferir **branches de feature** e Pull Requests para `main` em vez de commits diretos na `main`. Detalhes em `docs/contributing.md`.

## Referências visuais (local)
A pasta `ref/` está no `.gitignore` e pode conter HTML estático e imagens para alinhar layout com o plano do projeto; não entra no repositório.

## Quick start
1. Copy env file:
   - `cp .env.example .env`
2. Start stack:
   - `docker compose up --build`
3. Run migration:
   - `docker compose run --rm backend php bin/migrate.php`
4. (Optional) Load demo data for the default login user (`user_id = 1`):
   - `docker compose run --rm backend php bin/seed_demo.php`
5. Access:
   - API: `http://localhost:8080`
   - Frontend: `http://localhost:5173`

Demo seed inserts study plans whose titles start with `[demo]` (removed and re-created on each run) and weekly goals for the current ISO week.

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
- `GET /weekly-goals`, `PUT /weekly-goals` (query `week=YYYY-Www` opcional)
- `GET /weekly-progress` (query `week=YYYY-Www` opcional)

**Weekly progress:** os números em `counts` vêm da agregação de `activity_events` na semana (não mais de `updated_at` em `study_plans`). Migração: `003_create_activity_events.sql`.

## Testing
- Backend: `cd backend && composer install && vendor/bin/phpunit`
- Frontend: `cd frontend && npm install && npm run test -- --run`
- E2E (Playwright): `cd frontend && npm run test:e2e:install` (once). Start the stack (`docker compose up`), ensure `VITE_API_URL` matches the API (default `http://localhost:8080`). Build and preview the front (`npm run build && npm run preview`), then in another terminal `cd frontend && npm run test:e2e`. CI runs the same flow against MySQL + PHP + Vite preview (see `.github/workflows/ci.yml`).

## Cron
Run overdue marker manually:
- `cd backend && php bin/cron_mark_overdue.php`

(For production, schedule this command daily in crontab.)

## Daily summary e-mail (CLI)
Sends a plain-text and HTML summary with **dashboard totals by status** for `DAILY_SUMMARY_USER_ID` (default `1`, demo user) to `MAIL_TO`, using Symfony Mailer and `MAILER_DSN`.

1. Set in `.env` (see `.env.example`): `MAIL_TO`, `MAILER_DSN`, optionally `MAIL_FROM`, `MAIL_FROM_NAME`, `DAILY_SUMMARY_ENABLED`.
2. Run (Docker): `docker compose run --rm backend php bin/send_daily_summary.php`
3. If `DAILY_SUMMARY_ENABLED` is false or `MAIL_TO` / `MAILER_DSN` are empty, the script exits without sending (safe for local dev).

Example `MAILER_DSN` for a local Mailpit/Mailhog SMTP: `smtp://localhost:1025`

Schedule daily next to the overdue job in crontab or your orchestrator.
