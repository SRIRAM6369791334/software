<?php

$modelsDir = 'app/Models';
$models = scandir($modelsDir);

foreach ($models as $model) {
    if ($model === '.' || $model === '..') continue;
    
    $path = "$modelsDir/$model";
    $content = file_get_contents($path);
    
    if (strpos($content, 'HasFactory') === false) {
        // Add use statement
        $content = preg_replace('/namespace App\\\\Models;/', "namespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Factories\\HasFactory;", $content);
        
        // Add use trait inside class
        $content = preg_replace('/class (\w+) extends Model\s*{/', "class $1 extends Model\n{\n    use HasFactory;", $content);
        
        // For User model which extends Authenticatable
        $content = preg_replace('/class (\w+) extends Authenticatable\s*{/', "class $1 extends Authenticatable\n{\n    use HasFactory;", $content);
        
        file_put_contents($path, $content);
        echo "Updated $model\n";
    }
}
