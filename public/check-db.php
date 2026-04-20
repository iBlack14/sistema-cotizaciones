<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Quotation;

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');

try {
    echo "Check database state...\n";
    
    // 1. Check if column exists
    $hasColumn = Schema::hasColumn('quotations', 'slug');
    echo "Quotation has 'slug' column: " . ($hasColumn ? 'YES' : 'NO') . "\n";
    
    if ($hasColumn) {
        // 2. Check latest quotation
        $latest = Quotation::latest()->first();
        if ($latest) {
            echo "Latest Quotation ID: " . $latest->id . "\n";
            echo "Slug: " . ($latest->slug ?: '[NULL]') . "\n";
        } else {
            echo "No quotations found.\n";
        }
    } else {
        echo "Column 'slug' does not exist in 'quotations' table.\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
