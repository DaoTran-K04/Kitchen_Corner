<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$recipes = \App\Models\Recipe::where('status', 'published')
    ->where(function($q) {
        $q->whereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\bcá\\\\b'")
          ->orWhereHas('ingredients', function($ing) {
              $ing->whereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\bcá\\\\b'");
          });
    })->pluck('title');

echo json_encode($recipes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
