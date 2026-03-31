# Contributing

## Branches
- **`main`**: branch padrão estável; idealmente recebe mudanças via **Pull Request**.
- **Feature branches**: use nomes descritivos, por exemplo:
  - `feat/weekly-goals-ui`
  - `fix/phpunit-ci`
  - `docs/roadmap-update`

Fluxo sugerido:
1. `git checkout main && git pull`
2. `git checkout -b feat/nome-da-feature`
3. Commits pequenos e focados
4. Abrir PR para `main` (ou merge local com revisão própria)

## Referências visuais (locais)
A pasta `ref/` no repositório está no `.gitignore` e serve para **HTML estático de referência** e imagens de layout (não versionadas). Use para alinhar UI com o plano do projeto sem poluir `frontend/src`.

## Documentação de planejamento
Arquivos como `PROJECT_PLAN.md` podem ficar em `ref/` se preferir não versionar; o roadmap público continua em `docs/roadmap.md`.
