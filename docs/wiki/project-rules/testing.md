# Testing Strategy & Quality Assurance

## 1. Testing Framework
- **Test Runner**: Pest PHP (preferred) or PHPUnit.
- **Execution Command**: `php artisan test` or `./vendor/bin/pest`.
- **Database Refresh**: Use `RefreshDatabase` trait in integration/feature tests. Use SQLite in-memory or dedicated test database (`.env.testing`).

## 2. Test Hierarchy & Scope
- **Unit Tests (`tests/Unit/`)**:
  - Test pure logic, domain services, value objects, and helper utilities.
  - Do NOT touch the database or external network services in unit tests; use mocks/spies if necessary.
- **Feature Tests (`tests/Feature/`)**:
  - Test HTTP endpoints, controller flows, form validation errors, authorization rules, and database persistence.
  - Assert HTTP status codes, JSON response schemas, session flash messages, and database state (`assertDatabaseHas`).
- **End-to-End / Browser Tests**:
  - Use Playwright MCP or Laravel Dusk for end-to-end critical user journeys (login, checkout, form submission).

## 3. Test Naming & Structure (Arrange-Act-Assert)
- Test descriptions must read like natural specifications:
  ```php
  it('prevents unauthorized user from updating profile', function () {
      // Arrange
      $user = User::factory()->create();
      $anotherUser = User::factory()->create();

      // Act
      $response = $this->actingAs($user)->putJson("/api/users/{$anotherUser->id}", [
          'name' => 'New Name'
      ]);

      // Assert
      $response->assertStatus(403);
  });
  ```

## 4. Coverage Expectations
- Aim for high coverage on critical business logic, authorization policies, and payment/auth flows (minimum 80% coverage on domain and services).
