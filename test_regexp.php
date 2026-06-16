<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function testRegexp($keyword) {
    $recipes = \App\Models\Recipe::where('status', 'published')
        ->where(function($q) use ($keyword) {
            $q->whereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\b{$keyword}\\\\b'")
              ->orWhereHas('ingredients', function($ing) use ($keyword) {
                  $ing->whereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\b{$keyword}\\\\b'");
              });
        })->pluck('title');
    echo "$keyword matches: " . count($recipes) . " recipes\n";
}

testRegexp('cá');
testRegexp('gà');
testRegexp('bò');
testRegexp('heo');
testRegexp('bánh');
