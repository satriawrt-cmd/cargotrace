<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$compiler = app('blade.compiler');

$views = glob('resources/views/**/*.blade.php');
$views = array_merge($views, glob('resources/views/**/**/*.blade.php'));
$views = array_merge($views, glob('resources/views/**/**/**/*.blade.php'));
$views = array_unique($views);

$hasError = false;
foreach ($views as $file) {
    try {
        $content = file_get_contents($file);
        $compiled = $compiler->compileString($content);
        // check php syntax of compiled string
        $tempFile = tempnam(sys_get_temp_dir(), 'blade_test_');
        file_put_contents($tempFile, $compiled);
        exec("C:\\xampp\\php\\php.exe -l " . escapeshellarg($tempFile) . " 2>&1", $output, $returnVar);
        if ($returnVar !== 0) {
            echo "ERROR in $file:\n" . implode("\n", $output) . "\n";
            $hasError = true;
        }
        unlink($tempFile);
    } catch (\Throwable $e) {
        echo "EXCEPTION in $file: " . $e->getMessage() . "\n";
        $hasError = true;
    }
}

if (!$hasError) {
    echo "ALL BLADE VIEWS COMPILE CLEANLY!\n";
}
