<?php
/**
 * Script de emergencia para ejecutar migraciones
 */

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Inicializar la aplicación para poder usar Facades
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');

try {
    echo "Iniciando proceso...\n";
    
    // 1. Ejecutar migraciones
    echo "Ejecutando php artisan migrate --force...\n";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    echo "Estado: " . ($exitCode === 0 ? "ÉXITO" : "ERROR ($exitCode)") . "\n";
    echo Artisan::output() . "\n";
    
    // 2. Limpiar cache
    echo "Limpiando cache...\n";
    Artisan::call('optimize:clear');
    echo Artisan::output() . "\n";
    
    echo "¡TODO LISTO!";
} catch (\Throwable $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
