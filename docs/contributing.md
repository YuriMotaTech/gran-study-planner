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

## Commits e PRs

- **Mensagens de commit:** preferir o imperativo e mensagens curtas que descrevam o que mudou (ex.: *Add weekly goals panel*). Opcionalmente use [Conventional Commits](https://www.conventionalcommits.org/) para leitura do histórico: `feat:`, `fix:`, `chore:`, `docs:`, `test:`.
- **Um PR = uma entrega lógica:** evite misturar uma mudança grande de produto com ajustes de tooling ou formatação no mesmo PR; isso facilita revisão, revert e `git bisect`.
- **Commits pequenos** dentro do mesmo PR são bem-vindos; o PR deve ter uma descrição que amarre o objetivo (e o link para o item do roadmap, se aplicável).
- **Roadmap:** para mudanças de produto, mencione `docs/roadmap.md` na descrição do PR quando fechar ou avançar um item, para manter rastreabilidade.

## Referências visuais (locais)
A pasta `ref/` no repositório está no `.gitignore` e serve para **HTML estático de referência** e imagens de layout (não versionadas). Use para alinhar UI com o plano do projeto sem poluir `frontend/src`.

## Documentação de planejamento
Arquivos como `PROJECT_PLAN.md` podem ficar em `ref/` se preferir não versionar; o roadmap público continua em `docs/roadmap.md`.
