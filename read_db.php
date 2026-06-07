<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$messages = App\Models\ChatMessage::orderBy('id', 'desc')->take(10)->get();
foreach ($messages as $m) {
    echo "[" . $m->role . "] " . $m->content . "\n---\n";
}
