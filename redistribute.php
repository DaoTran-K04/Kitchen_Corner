<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Recipe;

$userIds = User::pluck('id')->toArray();
if (count($userIds) > 0) {
    Recipe::all()->each(function($r) use ($userIds) {
        $r->user_id = $userIds[array_rand($userIds)];
        $r->save();
    });
    echo "Phân bổ lại thành công cho " . count($userIds) . " tác giả.\n";
}
