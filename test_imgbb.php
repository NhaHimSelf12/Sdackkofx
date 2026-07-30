<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$base64 = base64_encode(file_get_contents('storage/app/public/avatars/J2qgt61Y47Lvz3LMj0zQzE9qL7Yl5yzpaEz5e8wc.jpg'));
$response = Illuminate\Support\Facades\Http::asForm()->post('https://api.imgbb.com/1/upload', [
    'key' => 'd732381816a6c92870089b0af3474706',
    'image' => $base64,
]);

echo json_encode([
    'status' => $response->status(),
    'body' => $response->json(),
], JSON_PRETTY_PRINT);
