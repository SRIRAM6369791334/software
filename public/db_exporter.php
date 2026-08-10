<?php

/*
|--------------------------------------------------------------------------
| Standalone Database Schema & Data Exporter for Live Server
|--------------------------------------------------------------------------
| Usage: Upload this file to your public/ directory or root directory on live server.
| Access in browser: http://your-domain.com/db_exporter.php
| It will export all database tables (Schema CREATE TABLE + Data INSERT statements)
| and automatically trigger a download of the .sql file.
*/

define('LARAVEL_START', microtime(true));

// Load Autoloader & Bootstrap Laravel Application to use DB credentials from .env
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

try {
    $driver = DB::connection()->getDriverName();
    $dbName = DB::connection()->getDatabaseName();
    $filename = 'db_export_' . ($dbName ?: 'backup') . '_' . date('Y-m-d_H-i-s') . '.sql';

    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    if (ob_get_level()) {
        ob_end_clean();
    }

    $pdo = DB::connection()->getPdo();

    echo "-- ========================================================\n";
    echo "-- Standalone Database Export ({$driver})\n";
    echo "-- Database Name: {$dbName}\n";
    echo "-- Exported Date: " . date('Y-m-d H:i:s') . "\n";
    echo "-- ========================================================\n\n";

    if ($driver === 'sqlite') {
        echo "PRAGMA foreign_keys = OFF;\nBEGIN TRANSACTION;\n\n";

        $stmt = $pdo->query("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tables as $t) {
            $tableName = $t['name'];
            $createSql = $t['sql'];

            echo "-- --------------------------------------------------------\n";
            echo "-- Table structure for table \"{$tableName}\"\n";
            echo "-- --------------------------------------------------------\n";
            echo "DROP TABLE IF EXISTS \"{$tableName}\";\n";
            echo $createSql . ";\n\n";

            echo "-- Dumping data for table \"{$tableName}\"\n";
            $dataStmt = $pdo->query("SELECT * FROM \"{$tableName}\"");
            while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
                $cols = array_map(fn($c) => "\"{$c}\"", array_keys($row));
                $vals = array_map(fn($v) => is_null($v) ? 'NULL' : $pdo->quote($v), array_values($row));
                echo "INSERT INTO \"{$tableName}\" (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
            }
            echo "\n\n";
            flush();
        }

        echo "COMMIT;\nPRAGMA foreign_keys = ON;\n";
    } else {
        // MySQL / MariaDB
        echo "SET FOREIGN_KEY_CHECKS=0;\n";
        echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        echo "SET AUTOCOMMIT = 0;\n";
        echo "START TRANSACTION;\n\n";

        $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tables = [];
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        foreach ($tables as $table) {
            echo "-- --------------------------------------------------------\n";
            echo "-- Table structure for table `{$table}`\n";
            echo "-- --------------------------------------------------------\n";
            echo "DROP TABLE IF EXISTS `{$table}`;\n";

            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $createRow = $createStmt->fetch(PDO::FETCH_ASSOC);
            $createSql = $createRow['Create Table'] ?? array_values($createRow)[1] ?? '';
            echo $createSql . ";\n\n";

            echo "-- Dumping data for table `{$table}`\n";
            $dataStmt = $pdo->query("SELECT * FROM `{$table}`");

            $batch = [];
            $batchSize = 100;
            $cols = [];

            while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
                if (empty($cols)) {
                    $cols = array_map(fn($c) => "`{$c}`", array_keys($row));
                }
                $vals = array_map(function ($val) use ($pdo) {
                    if (is_null($val)) return 'NULL';
                    return $pdo->quote($val);
                }, array_values($row));

                $batch[] = "(" . implode(", ", $vals) . ")";

                if (count($batch) >= $batchSize) {
                    echo "INSERT INTO `{$table}` (" . implode(", ", $cols) . ") VALUES\n" . implode(",\n", $batch) . ";\n";
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                echo "INSERT INTO `{$table}` (" . implode(", ", $cols) . ") VALUES\n" . implode(",\n", $batch) . ";\n";
            }

            echo "\n\n";
            flush();
        }

        echo "COMMIT;\n";
        echo "SET FOREIGN_KEY_CHECKS=1;\n";
    }
    exit;
} catch (\Exception $e) {
    echo "Database Export Error: " . $e->getMessage();
    exit;
}
