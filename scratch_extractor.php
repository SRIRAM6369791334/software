<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = [
    'models' => [],
    'controllers' => [],
    'routes' => []
];

// 1. Models
$modelPath = app_path('Models');
if (is_dir($modelPath)) {
    foreach (glob($modelPath . '/*.php') as $file) {
        $className = 'App\\Models\\' . basename($file, '.php');
        if (class_exists($className)) {
            $reflection = new ReflectionClass($className);
            $methods = [];
            foreach ($reflection->getMethods() as $method) {
                if ($method->class == $className) {
                    $methods[] = $method->getName();
                }
            }
            $output['models'][$className] = [
                'methods' => $methods,
            ];
            // Instantiate to get fillable
            try {
                $instance = new $className;
                $output['models'][$className]['fillable'] = $instance->getFillable();
                $output['models'][$className]['table'] = $instance->getTable();
            } catch (Exception $e) {}
        }
    }
}

// 2. Controllers
$controllerPath = app_path('Http/Controllers');
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllerPath));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() == 'php') {
        $relativePath = str_replace([$controllerPath . DIRECTORY_SEPARATOR, '.php', '/'], ['', '', '\\'], $file->getPathname());
        $className = 'App\\Http\\Controllers\\' . $relativePath;
        if (class_exists($className)) {
            $reflection = new ReflectionClass($className);
            $methods = [];
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->class == $className && !$method->isConstructor()) {
                    $methods[] = $method->getName();
                }
            }
            $output['controllers'][$className] = $methods;
        }
    }
}

// 3. Routes
$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    $output['routes'][] = [
        'method' => implode('|', $route->methods()),
        'uri' => $route->uri(),
        'name' => $route->getName(),
        'action' => $route->getActionName(),
        'middleware' => $route->middleware()
    ];
}

file_put_contents('scratch_dump.json', json_encode($output, JSON_PRETTY_PRINT));
echo "Done\n";
