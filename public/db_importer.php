<?php

/*
|--------------------------------------------------------------------------
| Standalone Live Server Database SQL Importer / Seeder
|--------------------------------------------------------------------------
| Usage:
| 1. Upload your .sql file (e.g. poultry_db (11).sql) into public/ directory on live server.
| 2. Upload this db_importer.php file into public/.
| 3. Access in browser: https://your-domain.com/db_importer.php
*/

define('LARAVEL_START', microtime(true));

set_time_limit(300);
ini_set('memory_limit', '512M');

// Load Autoloader & Bootstrap Laravel Application to use live DB credentials from .env
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
} elseif (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
} else {
    die("Error: Could not locate Laravel vendor autoloader.");
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Live Server Database Importer / Seeder</h2>";

try {
    // Look for .sql files in public directory
    $files = glob(__DIR__ . '/*.sql');

    if (empty($files)) {
        echo "<p style='color:red;'><strong>Error:</strong> No .sql file found in the <code>public/</code> folder!</p>";
        echo "<p>Please upload your <code>.sql</code> file (e.g., <code>poultry_db (11).sql</code>) into the <code>public/</code> folder on your live server and refresh this page.</p>";
        exit;
    }

    // Pick latest .sql file or specified file
    usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
    $sqlFile = $files[0];

    echo "<p>Found SQL File: <strong>" . htmlspecialchars(basename($sqlFile)) . "</strong> (" . number_format(filesize($sqlFile) / 1024, 2) . " KB)</p>";
    echo "<p>Importing into database: <strong>" . htmlspecialchars(DB::connection()->getDatabaseName()) . "</strong>...</p>";

    $sql = file_get_contents($sqlFile);
    if (empty($sql)) {
        echo "<p style='color:red;'><strong>Error:</strong> SQL file is empty!</p>";
        exit;
    }

    $pdo = DB::connection()->getPdo();
    $driver = DB::connection()->getDriverName();

    if ($driver === 'mysql' || $driver === 'mariadb') {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        $pdo->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

        // Wipe / Drop all existing tables first to ensure a completely fresh import
        $tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        while ($row = $tablesStmt->fetch(PDO::FETCH_NUM)) {
            $tableName = $row[0];
            $pdo->exec("DROP TABLE IF EXISTS `{$tableName}`;");
        }
        $viewsStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
        while ($row = $viewsStmt->fetch(PDO::FETCH_NUM)) {
            $viewName = $row[0];
            $pdo->exec("DROP VIEW IF EXISTS `{$viewName}`;");
        }
    } elseif ($driver === 'sqlite') {
        $pdo->exec("PRAGMA foreign_keys = OFF;");
        $tablesStmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        while ($row = $tablesStmt->fetch(PDO::FETCH_ASSOC)) {
            $tableName = $row['name'];
            $pdo->exec("DROP TABLE IF EXISTS \"{$tableName}\";");
        }
    }

    // Execute multi-statement SQL
    DB::unprepared($sql);

    if ($driver === 'mysql' || $driver === 'mariadb') {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    } elseif ($driver === 'sqlite') {
        $pdo->exec("PRAGMA foreign_keys = ON;");
    }

    echo "<h3 style='color:green;'>SUCCESS: Fresh Database imported & seeded successfully!</h3>";
    echo "<p>All existing tables were cleared, and fresh tables & values from <code>" . htmlspecialchars(basename($sqlFile)) . "</code> were imported.</p>";
} catch (\Exception $e) {
    echo "<h3 style='color:red;'>Import Failed:</h3>";
    echo "<pre style='background:#f8d7da; padding:10px; border-radius:5px;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

