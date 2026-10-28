# Git Workflow & Collaboration Standards

## 1. Branch Strategy
- **Protected Branch**: `main` (atau `master`) is production-ready. NEVER commit directly to `main`.
- **Branch Naming**: Use prefixed kebab-case branch names:
  - `feature/<short-description>` (e.g., `feature/user-auth`)
  - `fix/<short-description>` (e.g., `fix/login-csrf-mismatch`)
  - `refactor/<short-description>` (e.g., `refactor/order-service`)
  - `docs/<short-description>` (e.g., `docs/api-endpoints`)
  - `chore/<short-description>` (e.g., `chore/bump-dependencies`)

## 2. Conventional Commits
All commit messages must follow the Conventional Commits specification:
```text
<type>(<scope>): <short imperative summary>

[optional body explaining WHY and WHAT changed]

[optional footer, e.g., Closes #123]
```
Allowed Types:
- `feat`: A new feature for the user
- `fix`: A bug fix
- `refactor`: Code change that neither fixes a bug nor adds a feature
- `perf`: Code change that improves performance
- `test`: Adding missing tests or correcting existing tests
- `style`: Changes that do not affect the meaning of code (whitespace, formatting)
- `docs`: Documentation changes
- `chore`: Maintenance tasks, config, dependency updates

## 3. Pull Request (PR) Checklist
Before opening or merging a PR:
1. Linter & Formatter pass (`./vendor/bin/pint` & `npm run lint`).
2. All automated tests pass (`php artisan test`).
3. No secrets or local `.env` files staged.
4. Clean commit history without merge squabbles or uninformative messages ("wip", "fixed").
