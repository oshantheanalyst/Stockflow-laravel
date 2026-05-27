# StockFlow Security Architecture and Implementation

This document outlines the security measures implemented in the StockFlow web application, aligning with modern web application security standards and Laravel 12 best practices.

## 1. Authentication and Authorization

### Web Application Authentication
- **Package Used**: Laravel Jetstream with Fortify.
- **Mechanism**: Stateful, session-based authentication using cookies.
- **Implementation**: The standard `web` guard is used. Passwords are cryptographically hashed using **Bcrypt** (`Hash::make()`) before being stored in the database.
- **Protection**: Routes are protected by the `auth` middleware, redirecting unauthenticated users to the login screen.

### API Authentication
- **Package Used**: Laravel Sanctum.
- **Mechanism**: Stateless, token-based authentication.
- **Implementation**: API consumers must first authenticate via the `/api/auth/login` endpoint, which issues a Personal Access Token (PAT). This token must be passed as a Bearer token in the `Authorization` header for all subsequent API requests.
- **Protection**: All `/api/*` routes (except the login endpoint) are protected by the `auth:sanctum` middleware.

### Role-Based Access Control (RBAC)
- **Mechanism**: Custom User roles (`Admin` and `User`).
- **Implementation**:
  - The `User` model includes helper methods (`isAdmin()`, `canEdit()`, `canDelete()`).
  - A custom middleware `CheckAdmin` is used to protect administrative routes (e.g., User Management).
  - Business logic checks (e.g., `if (!auth()->user()->canEdit())`) restrict operations in Controllers and Livewire components.
  - An Admin cannot deactivate or delete their own account, preventing accidental lockouts.

## 2. API Security

- **Rate Limiting**: The `/api/auth/login` endpoint is rate-limited to 10 requests per minute per IP address (`throttle:10,1` middleware) to mitigate brute-force attacks.
- **Statelessness**: Sanctum tokens ensure that API requests do not rely on session state, reducing the risk of CSRF attacks against API endpoints.
- **Standardized Responses**: The API returns consistent JSON responses, never exposing stack traces or sensitive internal database errors to the client.

## 3. Data Protection and Validation

### Input Validation
- **Mechanism**: Laravel's built-in validation rules (`$request->validate()`).
- **Implementation**: All incoming data (both via Web and API) is strictly validated for type, length, format, and database constraints (e.g., `unique:customers,customer_code`). Invalid data is rejected with appropriate error messages (422 Unprocessable Entity).

### Protection Against SQL Injection
- **Mechanism**: Laravel's Eloquent ORM and Query Builder.
- **Implementation**: All database queries use PDO parameter binding behind the scenes. No raw SQL strings are constructed using user input.

### Protection Against Cross-Site Scripting (XSS)
- **Mechanism**: Blade Templating Engine.
- **Implementation**: Blade automatically escapes all data echoed using the `{{ $variable }}` syntax using `htmlspecialchars()`.

### Protection Against Cross-Site Request Forgery (CSRF)
- **Mechanism**: Laravel's `validateCsrfTokens` middleware.
- **Implementation**: All state-changing web form requests (POST, PUT, DELETE via Blade) are protected by CSRF token validation. The `@csrf` directive is included in every Blade form and is verified server-side on submission.
- **Exemptions** (in `bootstrap/app.php`):
  - `/api/*` — exempt because API endpoints require a Sanctum Bearer token in the `Authorization` header, which cannot be forged cross-site.
  - `/livewire/update`, `/livewire/upload-file`, `/livewire/preview-file/*` — exempt because Livewire v3's internal mechanisms use component checksums and are authenticated via the existing session/token.

## 4. Business Logic Security

- **Soft Deletion Mechanism**: Records like Users, Customers, Suppliers, and Products are not permanently deleted from the database. Instead, an `is_active` or `is_deleted` flag is toggled. This ensures auditability and prevents accidental data loss.
- **Transactional Integrity**: Financial operations, such as creating an invoice (`SalesController` / `NewSale` Livewire), are wrapped in database transactions (`DB::transaction`). This ensures that if stock deduction fails, the invoice creation is rolled back, preventing data inconsistencies.

## 5. Security Headers and Environment Configuration

- **Environment Variables**: Sensitive configuration (Database credentials, API keys, Application Key) are stored in the `.env` file, which is excluded from version control (`.gitignore`).
- **App Debug Mode**: In a production environment, `APP_DEBUG` must be set to `false` to prevent the leakage of sensitive application structure and stack traces to end users.
- **Secure Cookies**: The `SESSION_SECURE_COOKIE` is utilized to ensure session cookies are only transmitted over secure HTTPS connections (configurable via `.env`).

## 6. External API Security
- **Caching**: The currency conversion API integration utilizes Laravel's `Cache` facade. Rates are cached for 1 hour to prevent excessive outbound requests and potential rate-limiting or IP blocking by the external provider.
- **Timeouts**: HTTP requests to the external API include a strict timeout (`Http::timeout(10)`) to prevent the application from hanging if the external service is unresponsive.
