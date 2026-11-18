# Backend Security & OWASP Top 10 Guidelines

## 1. Input Validation & Sanitization
- **Strict Validation**: Validate ALL incoming requests using dedicated Form Request classes. Reject unknown or unexpected parameters.
- **Mass Assignment Protection**: Always declare explicit `$fillable` or `$guarded = ['*']` properties in Eloquent models. Never use unvalidated `$request->all()` in `Model::create()` or `update()`.

## 2. Authentication & Authorization
- **Auth Protocols**: Use Laravel Sanctum or standard session authentication.
- **Policies & Gates**: Protect controller actions with Laravel Policies (`$this->authorize('update', $post)`). Never rely solely on UI-level hiding of buttons or links.
- **Session Security**: Enforce HTTPS, secure cookies (`SESSION_SECURE_COOKIE=true`), and regenerate session IDs upon login (`$request->session()->regenerate()`).

## 3. SQL Injection & Database Security
- **Eloquent / Query Builder**: Rely on Eloquent or Query Builder parameter binding.
- **Raw Queries**: Never concatenate unsanitized user inputs into `DB::raw()`, `whereRaw()`, or `selectRaw()`. Always pass bindings as second parameters:
  ```php
  DB::table('users')->whereRaw('status = ?', [$status])
  ```

## 4. XSS & CSRF Protection
- **Blade Escaping**: Always use standard `{{ $variable }}` which automatically runs `htmlspecialchars()`. Avoid unescaped `{!! $variable !!}` unless HTML is thoroughly sanitized via an approved HTML purifier.
- **CSRF Tokens**: Ensure `@csrf` directive is present in all HTML `<form>` submissions. For Axios/Fetch requests, include `X-CSRF-TOKEN` or `X-XSRF-TOKEN`.

## 5. Rate Limiting & Denial of Service
- Configure Laravel Rate Limiter middleware (`throttle:api`, `throttle:login`) on sensitive routes (auth, password reset, payment, API search).

## 6. Error Handling & Logging
- **Production Error Masking**: Set `APP_DEBUG=false` in staging and production to prevent stack trace leaks.
- **Secure Logging**: Use `Log::error()` or `Log::warning()` with contextual arrays. Mask sensitive keys (`password`, `credit_card`, `token`, `api_key`) before logging.
