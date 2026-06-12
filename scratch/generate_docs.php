<?php
$baseDir = realpath(__DIR__ . '/../');
$appDir = $baseDir . '/app';
$routesDir = $baseDir . '/routes';

$outputFile = 'C:/Users/srira/.gemini/antigravity/brain/ca2035a5-3548-4eda-a804-abae5e691d45/C4-Documentation/all_files_functions.md';

$markdown = "# Code-Level Architecture & File Audit\n\n";
$markdown .= "This document contains an audit of **each and every file** and its functions in the `app` and `routes` directories.\n\n";

function scanDirectory($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$allFiles = array_merge(scanDirectory($appDir), scanDirectory($routesDir));

foreach ($allFiles as $file) {
    $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $file);
    $markdown .= "## File: `" . str_replace('\\', '/', $relativePath) . "`\n";
    
    $content = file_get_contents($file);
    
    // Extract namespace
    preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatch);
    $namespace = $namespaceMatch[1] ?? 'Global';
    
    // Extract class names
    preg_match('/(?:class|interface|trait)\s+(\w+)/', $content, $classMatch);
    $className = $classMatch[1] ?? null;
    
    if ($className) {
        $markdown .= "**Class:** `" . $className . "` (Namespace: `" . $namespace . "`)\n\n";
        
        // Match public/protected/private methods
        preg_match_all('/(?:public|protected|private)\s+function\s+(\w+)\s*\((.*?)\)/', $content, $methodMatches, PREG_SET_ORDER);
        
        if (!empty($methodMatches)) {
            $markdown .= "**Functions/Methods:**\n";
            foreach ($methodMatches as $match) {
                $markdown .= "- `" . $match[1] . "(" . $match[2] . ")`\n";
            }
        } else {
            $markdown .= "*No explicit methods found.*\n";
        }
    } else {
        // If it's a route file or helper without a class
        $markdown .= "*Type: Procedural / Route File*\n\n";
        
        // Look for functions
        preg_match_all('/function\s+(\w+)\s*\((.*?)\)/', $content, $funcMatches, PREG_SET_ORDER);
        if (!empty($funcMatches)) {
            $markdown .= "**Functions:**\n";
            foreach ($funcMatches as $match) {
                $markdown .= "- `" . $match[1] . "(" . $match[2] . ")`\n";
            }
        }
        
        // Look for route definitions
        preg_match_all('/Route::(\w+)\s*\(\s*\'([^\']+)\'/', $content, $routeMatches, PREG_SET_ORDER);
        if (!empty($routeMatches)) {
            $markdown .= "**Defined Routes:**\n";
            foreach ($routeMatches as $match) {
                $markdown .= "- `[" . strtoupper($match[1]) . "] " . $match[2] . "`\n";
            }
        }
    }
    $markdown .= "\n---\n\n";
}

// Generate an architecture diagram based on directories
$markdown .= "## Architecture Diagram (Flow)\n\n";
$markdown .= "```mermaid\n";
$markdown .= "graph TD\n";
$markdown .= "    Routes[\"Routes (web.php, api.php)\"]\n";
$markdown .= "    Controllers[\"Controllers (app/Http/Controllers/)\"]\n";
$markdown .= "    Requests[\"Form Requests (app/Http/Requests/)\"]\n";
$markdown .= "    Services[\"Services (app/Services/)\"]\n";
$markdown .= "    Models[\"Models (app/Models/)\"]\n";
$markdown .= "    Database[\"Database\"]\n";
$markdown .= "    Routes --> Controllers\n";
$markdown .= "    Controllers --> Requests\n";
$markdown .= "    Controllers --> Services\n";
$markdown .= "    Controllers --> Models\n";
$markdown .= "    Services --> Models\n";
$markdown .= "    Models --> Database\n";
$markdown .= "```\n";

if (!is_dir(dirname($outputFile))) {
    mkdir(dirname($outputFile), 0777, true);
}
file_put_contents($outputFile, $markdown);
echo "Documentation generated at: " . $outputFile;
