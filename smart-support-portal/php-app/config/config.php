<?php
// php-app/config/config.php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Load env vars (works in Docker containers or local env)
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: '5432';
$dbName = getenv('DB_NAME') ?: 'support';
$dbUser = getenv('DB_USER') ?: 'postgres';
$dbPass = getenv('DB_PASSWORD') ?: '';

$analyzerHost = getenv('ANALYZER_HOST') ?: 'python-analyzer';
$analyzerPort = getenv('ANALYZER_PORT') ?: '8000';

$redisHost = getenv('REDIS_HOST') ?: null;
$redisPort = getenv('REDIS_PORT') ?: '6379';

$dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    // Simple error page
    http_response_code(500);
    echo "<h1>Database connection failed</h1><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}

// Redis (if available)
$redis = null;
if ($redisHost) {
    if (class_exists('Redis')) {
        try {
            $redis = new Redis();
            $redis->connect($redisHost, (int)$redisPort, 2.0);
            // Configure PHP session to use Redis if available
            ini_set('session.save_handler', 'redis');
            ini_set('session.save_path', "tcp://{$redisHost}:{$redisPort}");
        } catch (Exception $e) {
            // ignore redis if connection fails
            $redis = null;
        }
    } else {
        // phpredis not installed; still continue without session storage in redis
        $redis = null;
    }
}

// Start session after configuring session save handler
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helpers: view and redirect
function view(string $viewPath, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $content = __DIR__ . '/../src/Views/' . $viewPath;
    ob_start();
    require $content;
    $body = ob_get_clean();
    require __DIR__ . '/../src/Views/layout.php';
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function flash(string $key, $value = null)
{
    if ($value === null) {
        if (isset($_SESSION['_flash'][$key])) {
            $v = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $v;
        }
        return null;
    }
    $_SESSION['_flash'][$key] = $value;
}
