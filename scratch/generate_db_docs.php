<?php
$baseDir = realpath(__DIR__ . '/../');
$migrationsDir = $baseDir . '/database/migrations';
$outputFile = 'C:/Users/srira/.gemini/antigravity/brain/ca2035a5-3548-4eda-a804-abae5e691d45/C4-Documentation/database_schema.md';

$markdown = "# Database Schema & Table Structures\n\n";
$markdown .= "This document details the database tables, their columns, and the Entity-Relationship (ER) architecture flow.\n\n";

$tables = [];

if (is_dir($migrationsDir)) {
    $files = scandir($migrationsDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $content = file_get_contents($migrationsDir . '/' . $file);
            
            // Extract table name
            if (preg_match('/Schema::create\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $tableMatch)) {
                $tableName = $tableMatch[1];
                $tables[$tableName] = [];
                
                // Extract columns
                preg_match_all('/\$table->([a-zA-Z0-9_]+)\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $colMatches, PREG_SET_ORDER);
                foreach ($colMatches as $match) {
                    $type = $match[1];
                    $name = $match[2];
                    $tables[$tableName][] = ['name' => $name, 'type' => $type];
                }
                
                // Add id and timestamps if they exist
                if (strpos($content, '$table->id()') !== false) {
                    array_unshift($tables[$tableName], ['name' => 'id', 'type' => 'bigIncrements']);
                }
                if (strpos($content, '$table->timestamps()') !== false) {
                    $tables[$tableName][] = ['name' => 'created_at', 'type' => 'timestamp'];
                    $tables[$tableName][] = ['name' => 'updated_at', 'type' => 'timestamp'];
                }
            }
        }
    }
}

$markdown .= "## Entity-Relationship (ER) Diagram\n\n";
$markdown .= "```mermaid\n";
$markdown .= "erDiagram\n";

foreach ($tables as $tableName => $columns) {
    $markdown .= "    " . strtoupper($tableName) . " {\n";
    foreach ($columns as $col) {
        $markdown .= "        " . $col['type'] . " " . $col['name'] . "\n";
    }
    $markdown .= "    }\n";
}

// Add some known relationships based on common conventions
$markdown .= "    USERS ||--o{ ACTIVITY_LOGS : generates\n";
$markdown .= "    CUSTOMERS ||--o{ BILLS : has\n";
$markdown .= "    DEALERS ||--o{ DEALER_PAYMENTS : makes\n";
$markdown .= "    VENDORS ||--o{ PURCHASES : supplies\n";
$markdown .= "    PURCHASES ||--o{ STOCK : updates\n";
$markdown .= "```\n\n";

$markdown .= "## Table Details\n\n";
foreach ($tables as $tableName => $columns) {
    $markdown .= "### Table: `" . $tableName . "`\n";
    $markdown .= "| Column Name | Data Type |\n";
    $markdown .= "|-------------|-----------|\n";
    foreach ($columns as $col) {
        $markdown .= "| `" . $col['name'] . "` | `" . $col['type'] . "` |\n";
    }
    $markdown .= "\n";
}

$markdown .= "## Architecture Flow (How It Works)\n\n";
$markdown .= "```mermaid\n";
$markdown .= "sequenceDiagram\n";
$markdown .= "    actor User\n";
$markdown .= "    participant WebUI as Web Interface\n";
$markdown .= "    participant Controller as Laravel Controller\n";
$markdown .= "    participant Service as Business Logic (Service)\n";
$markdown .= "    participant Model as Eloquent Model\n";
$markdown .= "    participant DB as MySQL Database\n";
$markdown .= "    \n";
$markdown .= "    User->>WebUI: Clicks 'Create Bill'\n";
$markdown .= "    WebUI->>Controller: POST /billing/daily (Form Request)\n";
$markdown .= "    Controller->>Controller: Validates Data ($request->validate())\n";
$markdown .= "    Controller->>Service: processBilling($data)\n";
$markdown .= "    Service->>Model: Bill::create()\n";
$markdown .= "    Model->>DB: INSERT query\n";
$markdown .= "    DB-->>Model: Return ID\n";
$markdown .= "    Service->>Model: Update Stock / Ledger\n";
$markdown .= "    Model->>DB: UPDATE query\n";
$markdown .= "    Service-->>Controller: Return Success\n";
$markdown .= "    Controller-->>WebUI: Redirect with Success Message\n";
$markdown .= "    WebUI-->>User: Shows 'Bill Created' UI\n";
$markdown .= "```\n";

if (!is_dir(dirname($outputFile))) {
    mkdir(dirname($outputFile), 0777, true);
}
file_put_contents($outputFile, $markdown);
echo "Database documentation generated at: " . $outputFile;
