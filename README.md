# PHP MVC Framework

This PHP MVC Framework is a custom implementation of the Model-View-Controller pattern using native PHP.

## Setup Instructions

1. **Clone the Repository**
   - Clone the repository:
     ```sh
     git clone https://github.com/yogaap24/php-mvc.git
     cd php-mvc
     ```

2. **Set Up the Database**
   - Navigate to the `sql` folder and choose the appropriate SQL file based on your database system:
     - For MySQL: Execute `sql/database.sql` in your MySQL client or import it using a database management tool.
     - For PostgreSQL: Execute `sql/databasepg.sql` in your PostgreSQL client or import it using a database management tool.

3. **Configure the Application**
   - Duplicate `config.example.yml` and rename it to `config.yml`.
   - Edit `config.yml` to match your environment settings:
     ```yaml
     app:
       environment: local
       debug: true

     database:
       db_name: your_database
       host: 127.0.0.1
       port: 3306
       user: your_username
       password: your_password
     ```

4. **Define Routes**
   - **Direct Route**: Define a route directly by specifying the HTTP method, URI, controller, and method:
     ```php
     Router::add('GET', '/', ExampleController::class, 'index');
     Router::add('POST', '/', ExampleController::class, 'post');
     ```

   - **Group Route**: Define a group of routes with a common prefix and controller. Optionally, specify middlewares as an array:
     ```php
     Router::group([
         'prefix' => 'home',        // URI prefix for the group
         'controller' => ExampleController::class, // Controller class for the group
         'middlewares' => [ExampleMiddleware::class]        // Optional array of middlewares
     ], function () {
         Router::add('GET', '/dashboard', 'dashboard');
     });
     ```

   - **Combination of Group and Direct Routes**: Combine grouped routes with direct routes:
     ```php
     Router::group([
         'prefix' => 'users',
         'controller' => AnotherExampleController::class
     ], function () {
         Router::add('GET', '/login', 'loginPage');
         Router::add('POST', '/login', 'login');

         Router::add('GET', '/', ExampleController::class, 'index');
     });
     ```

5. **Folder Descriptions**

   - **App**: Contains core application functionality, including routing and view management. The `View` class handles rendering templates and redirects, while routing configuration is managed by the `Routes` class.

   - **Config**: Holds configuration files, such as database settings and application environment variables. This folder helps manage application settings and adapt them to different environments.

   - **Domain**: Defines data models or entities that represent the core data structures of your application. These classes encapsulate and manage application data. For example:
     ```php
     namespace Yogaap\PHP\MVC\Domain;

     class Example
     {
         public string $id;
         public string $name;
         public string $value;
     }
     ```

   - **Helper**: Provides utility functions and classes that assist with common tasks throughout the application. Helpers are used to perform repetitive or complex operations in a reusable manner.

   - **Http**:
     - **Controllers**: Manages incoming requests and generates responses. Controllers interact with models and views to produce the desired output. They act as intermediaries between the application's data and the user's view.

     - **Requests**: Handles input validation and sanitization. These classes define the required data fields and validation rules, ensuring that incoming data is correct and safe for processing.

   - **Middleware**: Processes HTTP requests and responses, often used for tasks such as authentication, logging, and request modification. Middleware operates between the request and response cycle to handle additional logic.

   - **Repository**: Manages interactions with the database, including querying and updating records. Repositories encapsulate data access logic and provide a consistent interface for data operations, abstracting the details of database communication.

   - **Services**: Contains business logic and operations that process data and perform necessary actions within the application. Services handle the core functionality of the application, separating business logic from other layers such as controllers and repositories.

   - **View**: Holds view templates that generate the HTML output sent to the user. The view layer is responsible for presenting data to the user in a readable and structured format.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Acknowledgments

Special thanks to the PHP community for inspiration and guidance.

Feel free to contribute to this project by submitting issues or pull requests.
