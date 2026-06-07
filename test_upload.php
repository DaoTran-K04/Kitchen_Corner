<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$projectRef = 'uxkrgbnmvnzunxgkaunt';
$serviceRole = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InV4a3JnYm5tdm56dW54Z2thdW50Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc4MDM2Mjc1MiwiZXhwIjoyMDk1OTM4NzUyfQ.MNXPQcNUWESNjC-3Lflqj-jBB4rNpW-R_TrPFIXCCtI';

$file = public_path('assets/recipes/mealdb_53281.jpg');
$baseUrl = "https://{$projectRef}.supabase.co/storage/v1/object/recipes/mealdb_53281.jpg";

$mime = mime_content_type($file);
$response = \Illuminate\Support\Facades\Http::withToken($serviceRole)
    ->withBody(file_get_contents($file), $mime)
    ->post($baseUrl);

echo 'Status: ' . $response->status() . "\n";
echo 'Body: ' . $response->body() . "\n";
