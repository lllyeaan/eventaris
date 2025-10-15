<?php
declare(strict_types=1);

use App\Core\Database;

$basePath = dirname(__DIR__);

require $basePath . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';

$pdo = Database::connection();

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS migrations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )'
);

$migrationName = 'baseline_schema_v1';
$check = $pdo->prepare('SELECT COUNT(*) AS total FROM migrations WHERE migration = :migration LIMIT 1');
$check->bindValue(':migration', $migrationName);
$check->execute();
$result = $check->fetch(PDO::FETCH_ASSOC);

if (($result['total'] ?? 0) > 0) {
    fwrite(STDOUT, "Migration '{$migrationName}' already executed.\n");
    exit(0);
}

$sqlFile = $basePath . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . 'database.sql';
if (!file_exists($sqlFile)) {
    fwrite(STDERR, "Migration file not found: {$sqlFile}\n");
    exit(1);
}

$sqlContent = file_get_contents($sqlFile);
if ($sqlContent === false) {
    fwrite(STDERR, "Unable to read migration file.\n");
    exit(1);
}

$sqlContent = preg_replace('/^\s*--.*$/m', '', $sqlContent);
$sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);

$statements = array_filter(array_map(
    static fn(string $statement): string => trim($statement),
    preg_split('/;[\r\n]+/', $sqlContent) ?: []
));

$isComment = static function (string $sql): bool {
    $trimmed = ltrim($sql);
    return $trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*');
};

try {
    foreach ($statements as $statement) {
        if ($statement === '' || $isComment($statement)) {
            continue;
        }
        $pdo->exec($statement);
    }

    $batchResult = $pdo->query('SELECT COALESCE(MAX(batch), 0) AS batch FROM migrations');
    $batchValue = (int) ($batchResult->fetch(PDO::FETCH_ASSOC)['batch'] ?? 0) + 1;

    $insert = $pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)');
    $insert->bindValue(':migration', $migrationName);
    $insert->bindValue(':batch', $batchValue, PDO::PARAM_INT);
    $insert->execute();
} catch (Throwable $exception) {
    fwrite(STDERR, 'Migration failed: ' . $exception->getMessage() . PHP_EOL);
    logger('Migration failed: ' . $exception->getMessage());
    exit(1);
}

$logDir = storage_path('logs');
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
$logPath = $logDir . DIRECTORY_SEPARATOR . 'migrate.log';
$message = sprintf("[%s] Migration %s executed%s", (new DateTimeImmutable())->format('Y-m-d H:i:s'), $migrationName, PHP_EOL);
file_put_contents($logPath, $message, FILE_APPEND);

fwrite(STDOUT, "Migration '{$migrationName}' executed successfully.\n");
