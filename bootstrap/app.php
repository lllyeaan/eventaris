<?php
declare(strict_types=1);

use App\Core\Database;
use App\Core\Session;

$baseDirectory = dirname(__DIR__);

spl_autoload_register(static function (string $class) use ($baseDirectory): void {
    $prefix = 'App\\';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $path = $baseDirectory . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (file_exists($path)) {
        require $path;
    }
});

require_once __DIR__ . '/helpers.php';

load_env($baseDirectory);

$appConfig = require config_path('app.php');
$databaseConfig = require config_path('database.php');

set_config([
    'app' => $appConfig,
    'database' => $databaseConfig,
]);

date_default_timezone_set($appConfig['timezone'] ?? 'UTC');

Session::start();

Database::boot($databaseConfig);

return new App\Core\Router();
