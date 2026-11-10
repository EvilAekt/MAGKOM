# Code Style & Conventions

## 1. PHP & Laravel Standards
- **Standard**: Follow PSR-12 and Laravel Coding Guidelines (enforced by Laravel Pint).
- **Strict Types**: Use `declare(strict_types=1);` where applicable for domain and service classes.
- **Type Hinting**: Always specify parameter types and return types explicitly for every function and method:
  ```php
  public function findUserById(int $id): ?User
  ```

## 2. Naming Conventions
- **Models & Classes**: `PascalCase` (e.g., `UserProfile`, `OrderService`).
- **Controllers**: `PascalCase` with `Controller` suffix (e.g., `OrderController`).
- **Methods & Functions**: `camelCase` (e.g., `calculateTotal`, `getUserOrders`).
- **Database Tables & Columns**: `snake_case` plural for tables (`order_items`), `snake_case` singular for columns (`created_at`, `total_amount`).
- **Blade Views**: `kebab-case` or `snake_case` dot notation (e.g., `user.profile-card.blade.php`).
- **Config & Env Keys**: `SCREAMING_SNAKE_CASE` (e.g., `APP_ENV`, `DB_DATABASE`).

## 3. Architecture & Separation of Concerns
- **Skinny Controllers, Fat Models/Services**: Keep controllers thin. Do not write raw business logic or complex database queries inside controller actions; delegate to Service classes or Action classes.
- **Form Requests**: Never validate directly inside controller methods using `$request->validate()` for non-trivial endpoints; use dedicated `FormRequest` classes (`php artisan make:request StoreUserRequest`).
- **Data Transfer & Resources**: Use Eloquent API Resources (`JsonResource`) for JSON API responses to format outputs cleanly.

## 4. Frontend Code Conventions
- **JavaScript / TypeScript**: Modern ES6+, `camelCase` variables & functions, `PascalCase` component names.
- **CSS**: Tailwind CSS utility-first classes organized logically (layout -> spacing -> typography -> visual -> interactive). Avoid inline styles.
