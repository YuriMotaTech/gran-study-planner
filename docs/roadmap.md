# Public Roadmap

## v0.1.0 - Core MVP
- [x] Hexagonal backend structure (domain/use cases/adapters/interface)
- [x] CRUD for study plans
- [x] Dashboard stats endpoint
- [x] Redis cache with invalidation
- [x] Daily overdue cron job
- [x] React app with login, list, form, filters, status update, delete
- [x] Unit tests (PHP + React Testing Library)

## v0.2.0 - Robustness and DX
- [x] Simple authentication with token
- [x] Pagination and sorting in list API
- [x] Structured logs and `GET /health`
- [x] CI pipeline for backend/frontend tests
- [x] Add rate limiting middleware
- [x] Add request validation abstraction

## v0.3.0 - Product evolution
- [ ] Recurrence templates
- [x] Weekly goal tracking and progress charts
- [x] Activity event log adapter
- [ ] Notification channels (email/calendar export)
- [ ] Email notifications: daily study summary (due soon, overdue, and progress by status)

## v0.4.0 - Portfolio polish
- [ ] Seed data command
- [ ] End-to-end tests
- [ ] Demo video and architecture walkthrough
- [x] Internationalization (PT-BR / EN): toggle de idioma (ex.: bandeira BR para PT-BR, bandeira US para EN)
- [ ] Dark mode (tema claro/escuro persistido ou preferência do sistema)

## Git workflow (contribuição)
- Preferir **branches de feature** (`feat/...`, `fix/...`) e **Pull Requests** para `main`, em vez de commits diretos na `main`.
- Ver [contributing.md](contributing.md).
