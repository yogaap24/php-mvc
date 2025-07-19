# PHP MVC Framework

This PHP MVC Framework is a custom implementation of the Model-View-Controller pattern using native PHP with enhanced security features.

## Features

- ✅ **Secure Authentication System**
- ✅ **CSRF Protection**
- ✅ **Environment-based Configuration**
- ✅ **Universal SQL Migration System**
- ✅ **Advanced Validation Rules**
- ✅ **Session Management**
- ✅ **Flash Messaging**
- ✅ **Middleware Support**
- ✅ **Route Grouping**

## Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yogaap24/php-mvc.git
   cd php-mvc
   rm -rf .git
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Configuration**
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Edit `.env` to match your environment settings:
     ```env
     APP_ENV=local
     APP_DEBUG=true
     APP_URL=http://localhost

     DB_DRIVER=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=php_mvc
     DB_USERNAME=root
     DB_PASSWORD=your_password

     SESSION_COOKIE_NAME=X-YOGAAP-SESSION
     SESSION_LIFETIME=43200

     CSRF_TOKEN_NAME=_token
     ENCRYPTION_KEY=your-32-character-secret-key-here
     ```

4. **Database Setup**
   - Create your database
   - Run migrations:
     ```bash
     php console migrate
     ```
   - Check migration status:
     ```bash
     php console migrate:status
     ```

## Universal SQL Migration System

This system allows you to write **one SQL file** that works across multiple database systems (MySQL, PostgreSQL, SQLite).

### Supported Databases
- ✅ **MySQL** (default)
- ✅ **PostgreSQL** (set `DB_DRIVER=pgsql`)
- ✅ **SQLite** (set `DB_DRIVER=sqlite`)

### Migration Commands
```bash
# Run all pending migrations
php console migrate

# Check migration status
php console migrate:status

# Drop all tables and run fresh migrations
php console migrate:fresh

# Rollback specific migration
php console migrate:rollback 001_create_users.sql

# Show help
php console help
```

### Universal SQL Placeholders

#### Data Types
- `{{STRING}}` - VARCHAR/TEXT based on database
- `{{TEXT}}` - TEXT field
- `{{INT}}` - INTEGER/INT based on database
- `{{BIGINT}}` - BIGINT field
- `{{TIMESTAMP}}` - TIMESTAMP field
- `{{BOOLEAN}}` - BOOLEAN/INTEGER based on database

#### Functions
- `{{NOW}}` - Current timestamp
- `{{INTERVAL_12_HOURS}}` - Current time + 12 hours

#### Special Handling
- `ON UPDATE CURRENT_TIMESTAMP` - Automatically removed for PostgreSQL/SQLite
- `{{AUTO_INCREMENT}}` - Becomes SERIAL (PostgreSQL), AUTOINCREMENT (SQLite), AUTO_INCREMENT (MySQL)
- `{{UNIQUE_KEY}}` - Becomes CONSTRAINT (PostgreSQL), UNIQUE (SQLite), UNIQUE KEY (MySQL)

### Example Universal Migration

```sql
-- database/migrations/001_create_users.sql
CREATE TABLE IF NOT EXISTS users (
    id {{STRING}}(36) PRIMARY KEY,
    email {{STRING}}(255) UNIQUE NOT NULL,
    password {{STRING}}(255) NOT NULL,
    is_active {{BOOLEAN}} DEFAULT TRUE,
    created_at {{TIMESTAMP}} DEFAULT {{NOW}},
    updated_at {{TIMESTAMP}} DEFAULT {{NOW}} ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
    id {{AUTO_INCREMENT}},
    user_id {{STRING}}(36) NOT NULL,
    title {{STRING}}(255) NOT NULL,
    content {{TEXT}},
    published_at {{TIMESTAMP}} DEFAULT {{INTERVAL_12_HOURS}},
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_posts_user_id ON posts(user_id);
```

### Database-Specific Output

#### MySQL
```sql
CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### PostgreSQL
```sql
CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### SQLite
```sql
CREATE TABLE IF NOT EXISTS users (
    id TEXT PRIMARY KEY,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    is_active INTEGER DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT datetime('now'),
    updated_at TIMESTAMP DEFAULT datetime('now')
);
```

### Migration File Naming

Use descriptive names with numeric prefixes:
- `001_create_users_table.sql`
- `002_add_posts_table.sql`
- `003_add_user_avatar_column.sql`

### Migration Best Practices

1. **Always use placeholders** instead of database-specific syntax
2. **Test migrations** on all target databases
3. **Keep migrations small** and focused
4. **Use descriptive names** for clarity
5. **Create rollback files** for complex migrations

## Security Features

### CSRF Protection
All POST requests are automatically protected by CSRF tokens. Add to your forms:
```php
<?php
use Yogaap\PHP\MVC\Helper\CSRFToken;
echo CSRFToken::getHiddenInput();
?>
```

### Environment Variables
All sensitive configuration is now stored in `.env` file:
```php
// Get environment variable
$dbHost = env('DB_HOST', '127.0.0.1');
```

### Secure Session Management
- Session data is encrypted
- Automatic session timeout
- Session regeneration on authentication

### Input Validation
Advanced validation rules:
```php
$validator = new ValidationRules();
$errors = $validator->validate($data, [
    'email' => 'required|email|max:255',
    'password' => 'required|min:6|confirmed'
]);
```

## Route Definition

### Basic Routes
```php
Router::add('GET', '/', HomeController::class, 'index');
Router::add('POST', '/login', UserController::class, 'login', '', [CSRFMiddleware::class]);
```

### Route Groups
```php
Router::group(['prefix' => 'admin', 'middlewares' => [AuthMiddleware::class]], function () {
    Router::add('GET', '/dashboard', AdminController::class, 'dashboard');
    Router::add('GET', '/users', AdminController::class, 'users');
});
```

## Middleware

### Built-in Middleware
- `AuthMiddleware`: Authentication check
- `CSRFMiddleware`: CSRF token validation

### Custom Middleware
```php
class CustomMiddleware implements Middleware
{
    public function before(): void
    {
        // Middleware logic here
    }
}
```

## Validation Rules

Available validation rules:
- `required`: Field is required
- `email`: Must be valid email
- `min:n`: Minimum length
- `max:n`: Maximum length
- `confirmed`: Must match `field_confirmation`
- `same:field`: Must match another field
- `different:field`: Must be different from another field
- `numeric`: Must be numeric
- `integer`: Must be integer
- `url`: Must be valid URL
- `regex:pattern`: Must match regex pattern
- `in:val1,val2`: Must be in list
- `not_in:val1,val2`: Must not be in list
- `alpha`: Only letters
- `alpha_num`: Only letters and numbers
- `alpha_dash`: Only letters, numbers, dashes, underscores

## 📁 Directory Structure

```
php-mvc/
├── core/                    # Framework Core
│   ├── Foundation/          # Core application components
│   │   ├── Application.php  # Main application class
│   │   └── Container.php    # Dependency injection container
│   ├── Http/               # HTTP layer
│   │   ├── Router.php      # Static routing system
│   │   ├── Request.php     # HTTP request handling
│   │   └── Response.php    # HTTP response handling
│   ├── Service/            # Framework services
│   │   ├── BaseService.php # Abstract service base
│   │   ├── ResponseService.php # API response formatting
│   │   ├── SessionService.php # Session management
│   │   └── ValidationService.php # Validation utilities
│   ├── Middleware/         # Middleware components
│   │   ├── AuthMiddleware.php # Authentication
│   │   ├── CSRFMiddleware.php # CSRF protection
│   │   └── CorsMiddleware.php # CORS handling
│   ├── Security/           # Security utilities
│   │   ├── Session.php     # Session entity
│   │   ├── CSRF.php        # CSRF token management
│   │   └── Encryption.php  # Encryption utilities
│   ├── Database/           # Database layer
│   │   ├── Repository.php  # Repository base class
│   │   ├── Migration.php   # Migration management
│   │   └── SQLParser.php   # Universal SQL parser
│   ├── Console/            # CLI commands
│   │   └── MigrateCommand.php # Database migration
│   ├── Support/            # Helper utilities
│   │   └── FlashMessage.php # Flash messaging
│   └── View/               # View system
│       └── View.php        # Template rendering
├── app/                    # Application layer
│   ├── Config/             # Application configuration
│   │   ├── Database.php    # Database configuration
│   │   └── Environment.php # Environment handling
│   ├── Providers/          # Service providers
│   │   ├── CoreServiceProvider.php # Core services
│   │   └── DatabaseServiceProvider.php # Database services
│   ├── Exception/          # Exception handling
│   │   ├── AppException.php # Application exceptions
│   │   └── Handler.php     # Exception handler
│   └── bootstrap.php       # Application bootstrap
├── modules/                # Business modules
│   ├── Home/               # Home module
│   │   ├── Controller/     # Controllers
│   │   └── View/           # Module views
│   └── User/               # User module
│       ├── Controller/     # User controllers
│       ├── Service/        # Business logic
│       ├── Repository/     # Data access
│       ├── Entity/         # Domain entities
│       ├── DTO/            # Data transfer objects
│       └── View/           # User views
├── routes/                 # Route definitions
│   ├── web.php            # Web routes
│   └── api.php            # API routes
├── resources/              # Shared view resources
│   └── views/              # Application views
│       ├── layouts/        # Application layouts
│       │   └── main.php    # Main layout
│       ├── partials/       # Reusable view partials
│       │   ├── navigation.php # Navigation component
│       │   └── footer.php  # Footer component
│       └── components/     # UI components
│           ├── alert.php   # Alert component
│           └── button.php  # Button component
└── public/                # Web server root
    └── index.php          # Application entry point
```

## Development

### Running Tests
```bash
./vendor/bin/phpunit
```

### Code Standards
The project follows PSR-12 coding standards.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new features
5. Submit a pull request

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Acknowledgments

Special thanks to the PHP community for inspiration and guidance.

Feel free to contribute to this project by submitting issues or pull requests.
