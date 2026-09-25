<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$token = App\Models\WhatsappConfig::first()->api_key;
$token = Illuminate\Support\Facades\Crypt::decryptString($token);

$customerPhone = App\Models\WhatsappConfig::first()->bot_number;

$qrUrl = "https://quickchart.io/qr?text=Hello&size=300";

// Test 1: Multipart file upload using attach('file')
$imageContent = file_get_contents($qrUrl);
$response = Illuminate\Support\Facades\Http::withHeaders([
    'Authorization' => $token,
])
->attach('file', $imageContent, 'test.png')
->post('https://api.fonnte.com/send', [
    'target' => $customerPhone,
    'message' => 'Test attach file',
]);
echo "Test 1 (attach file): " . $response->body() . "\n";

// Test 2: url parameter as multipart text field
$response = Illuminate\Support\Facades\Http::withHeaders([
    'Authorization' => $token,
])
->post('https://api.fonnte.com/send', [
    'target' => $customerPhone,
    'message' => 'Test url form data',
    'url' => $qrUrl,
]);
echo "Test 2 (url json): " . $response->body() . "\n";

// Test 3: url parameter but Fonnte might need multipart instead of json
$response = Illuminate\Support\Facades\Http::asMultipart()->withHeaders([
    'Authorization' => $token,
])
->post('https://api.fonnte.com/send', [
    'target' => $customerPhone,
    'message' => 'Test url multipart',
    'url' => $qrUrl,
]);
echo "Test 3 (url multipart): " . $response->body() . "\n";

