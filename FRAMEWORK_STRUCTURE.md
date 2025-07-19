# PHP MVC Framework - Structure Overview

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

## 🚀 Key Features

### Static Router System
- **Simple Usage**: `Router::get('/path', 'Controller@method')`
- **Route Groups**: Support for middleware and prefix grouping
- **API Support**: Built-in API routing with CORS
- **Resource Routes**: RESTful resource routing

### Modular Architecture
- **Self-contained Modules**: Each module has its own controllers, services, views
- **Module Views**: Views are located within modules for better organization
- **Dependency Injection**: Clean dependency management

### Service Layer
- **ValidationService**: Centralized validation with rule parsing
- **SessionService**: Session management utilities
- **ResponseService**: Consistent API response formatting
- **BaseService**: Abstract base for module services

### Security Features
- **CSRF Protection**: Built-in CSRF middleware
- **Authentication**: Session-based authentication
- **Encryption**: Secure data encryption utilities
- **CORS Support**: API CORS handling

## 📋 Routing Examples

### Web Routes (routes/web.php)
```php
use Core\Http\Router;

// Simple route
Router::get('/', 'Modules\Home\Controller\HomeController@index');

// Route group with middleware
Router::group(['middleware' => ['Core\Middleware\AuthMiddleware']], function() {
    Router::get('/dashboard', 'Modules\Home\Controller\HomeController@dashboard');
});

// Route group with prefix
Router::group(['prefix' => 'users'], function() {
    Router::get('/profile', 'Modules\User\Controller\UserController@profile');
});
```

### API Routes (routes/api.php)
```php
use Core\Http\Router;

// Set API configuration (adds /api/v1 prefix and CORS)
Router::api('v1');

// API routes
Router::post('/auth/login', 'Modules\User\Controller\UserController@login');

// Protected API routes
Router::group(['middleware' => ['Core\Middleware\AuthMiddleware']], function() {
    Router::get('/users/profile', 'Modules\User\Controller\UserController@profile');
});

// Resource routes (RESTful)
Router::resource('posts', 'Modules\Post\Controller\PostController');
```

## 🔧 Validation Example

```php
use Core\Service\ValidationService;

// Using validation service
$errors = ValidationService::validate($data, [
    'email' => 'required|email',
    'password' => 'required|min:6|confirmed',
    'age' => 'required|integer|min:18'
]);
```

## 🏗️ Module Structure

Each module follows this pattern:
```
modules/ModuleName/
├── Controller/     # HTTP controllers
├── Service/        # Business logic
├── Repository/     # Data access layer
├── Entity/         # Domain entities
├── DTO/           # Data transfer objects
└── View/          # Module-specific views
```

This structure provides:
- **Clean separation of concerns**
- **Easy testing and maintenance**
- **Scalable architecture**
- **Modern PHP practices**