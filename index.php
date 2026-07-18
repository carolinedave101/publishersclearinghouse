<?php

define('LARAVEL_START', microtime(true));

// Auto-generate APP_KEY if none is set and persist it to .env immediately,
// so the key is stable across requests (avoids CSRF / session issues
// from a per-request temp key).
$envFile = __DIR__.'/pch/.env';
if (file_exists($envFile)) {
    $envContents = file_get_contents($envFile);
    if (!preg_match('/^APP_KEY=[a-zA-Z0-9+=\/:]+/m', $envContents)) {
        $tempKey = 'base64:'.base64_encode(random_bytes(32));
        $envContents = preg_replace('/^APP_KEY=.*/m', 'APP_KEY='.$tempKey, $envContents);
        file_put_contents($envFile, $envContents);
        $_ENV['APP_KEY'] = $tempKey;
        putenv('APP_KEY='.$tempKey);
    }
}

if (file_exists($maintenance = __DIR__.'/pch/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/pch/vendor/autoload.php';

$app = require_once __DIR__.'/pch/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
