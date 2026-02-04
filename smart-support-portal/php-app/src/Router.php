<?php
// php-app/src/Router.php
declare(strict_types=1);

class Router
{
    private PDO $pdo;
    private $redis;
    private string $analyzerHost;
    private string $analyzerPort;
    private array $routes;

    public function __construct(PDO $pdo, $redis = null, string $analyzerHost = 'python-analyzer', string $analyzerPort = '8000')
    {
        $this->pdo = $pdo;
        $this->redis = $redis;
        $this->analyzerHost = $analyzerHost;
        $this->analyzerPort = $analyzerPort;
        $this->routes = require __DIR__ . '/../config/routes.php';
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $uri = rtrim($uri, '/') ?: '/';

        $handler = $this->routes[$method][$uri] ?? null;
        if (!$handler) {
            http_response_code(404);
            echo "Not Found";
            exit;
        }

        [$controllerName, $action] = explode('@', $handler);
        $controllerFile = __DIR__ . '/Controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo "Controller $controllerName not found";
            exit;
        }
        // Instantiate controller
        require_once $controllerFile;
        $controller = new $controllerName($this->pdo, $this->redis, $this->analyzerHost, $this->analyzerPort);
        if (!method_exists($controller, $action)) {
            http_response_code(500);
            echo "Action $action not found in $controllerName";
            exit;
        }
        // Call action with exception handling to surface errors
        try {
            $controller->$action();
        } catch (Throwable $e) {
            http_response_code(500);
            echo "<h1>Server error</h1>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            error_log($e->getMessage());
            exit;
        }
    }
}
