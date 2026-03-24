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
- [ ] Add rate limiting middleware
- [ ] Add request validation abstraction

## v0.3.0 - Product evolution
- [ ] Recurrence templates
- [ ] Weekly goal tracking and progress charts
- [ ] Activity event log adapter
- [ ] Notification channels (email/calendar export)

## v0.4.0 - Portfolio polish
- [ ] Seed data command
- [ ] End-to-end tests
- [ ] Demo video and architecture walkthrough
