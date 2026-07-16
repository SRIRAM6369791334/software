<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    $tables = DB::select('SHOW TABLES');
    $outputPath = 'C:/Users/srira/.gemini/antigravity-ide/brain/d7e556a4-510d-43e9-9952-84386ded69ad/database_schema_and_values.md';
    $fp = fopen($outputPath, 'w');
    if (!$fp) {
        throw new \Exception("Could not open output path: " . $outputPath);
    }
    
    fwrite($fp, "# Database Schema and Values Report\n\n");
    fwrite($fp, "This report contains the schema (columns) and all rows for all tables in the `poultry_db` database.\n\n");
    
    foreach ($tables as $table) {
        $tableArray = (array)$table;
        $tableName = reset($tableArray);
        
        // Get columns
        $columns = Schema::getColumnListing($tableName);
        $count = DB::table($tableName)->count();
        
        fwrite($fp, "## Table: `$tableName` ($count rows)\n\n");
        fwrite($fp, "**Columns:** " . implode(', ', array_map(fn($c) => "`$c`", $columns)) . "\n\n");
        
        if ($count > 0) {
            // Get all rows
            $rows = DB::table($tableName)->get();
            
            // Format as Markdown table
            // Headers
            fwrite($fp, "| " . implode(' | ', $columns) . " |\n");
            // Separators
            fwrite($fp, "| " . implode(' | ', array_fill(0, count($columns), '---')) . " |\n");
            
            foreach ($rows as $row) {
                $rowArray = (array)$row;
                $rowValues = [];
                foreach ($columns as $col) {
                    $val = $rowArray[$col] ?? 'NULL';
                    // Escape pipe character and newlines for markdown table compatibility
                    if (is_string($val)) {
                        $val = str_replace(["|", "\r", "\n"], ["\\|", " ", " "], $val);
                    }
                    $rowValues[] = $val;
                }
                fwrite($fp, "| " . implode(' | ', $rowValues) . " |\n");
            }
            fwrite($fp, "\n");
        } else {
            fwrite($fp, "*This table is currently empty.*\n\n");
        }
        
        fwrite($fp, "---\n\n");
    }
    
    fclose($fp);
    echo "Database report successfully generated at: $outputPath\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
