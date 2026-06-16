<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$recipes = \App\Models\Recipe::where('status', 'published')
    ->where(function($q) {
        $q->whereRaw("LOWER(title) COLLATE utf8mb4_bin LIKE '%cá %'")
          ->orWhereRaw("LOWER(title) COLLATE utf8mb4_bin LIKE '% cá %'")
          ->orWhereRaw("LOWER(title) COLLATE utf8mb4_bin LIKE '% cá'")
          ->orWhereRaw("LOWER(title) COLLATE utf8mb4_bin = 'cá'")
          ->orWhereHas('ingredients', function($ing) {
              $ing->whereRaw("LOWER(name) COLLATE utf8mb4_bin LIKE '%cá %'")
                  ->orWhereRaw("LOWER(name) COLLATE utf8mb4_bin LIKE '% cá %'")
                  ->orWhereRaw("LOWER(name) COLLATE utf8mb4_bin LIKE '% cá'")
                  ->orWhereRaw("LOWER(name) COLLATE utf8mb4_bin = 'cá'");
          });
    })->pluck('title');

echo json_encode($recipes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
