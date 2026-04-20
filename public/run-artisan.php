<?php

declare(strict_types=1);

$token = $_GET['token'] ?? '';
$expectedToken = '2024902536a3253960cf067fdca5f04b94f5dd58c4bd457a7f30b0ef8c68d4a0';

if (!is_string($token) || !hash_equals($expectedToken, $token)) {
    http_response_code(403);
    exit('Forbidden');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$commands = [
    'key:generate --force',
    'migrate --force',
    'storage:link',
    'config:clear',
    'route:clear',
    'view:clear',
    'config:cache',
    'route:cache',
];

header('Content-Type: text/plain; charset=utf-8');

echo "Run started\n";
echo "IMPORTANT: Delete public/run-artisan.php after this.\n\n";

foreach ($commands as $command) {
    echo ">>> {$command}\n";
    $exitCode = $kernel->call($command);
    echo $kernel->output();
    echo "Exit code: {$exitCode}\n\n";
}

echo "Run completed.\n";
