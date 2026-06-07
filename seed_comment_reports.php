<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\User;
use App\Models\Recipe;

$users = User::where('role', 'user')->limit(3)->get();
if($users->count() < 2) die("Not enough users");

$reporter = clone $users[0];
$badUser = clone $users[1];

$recipe = Recipe::first();
if(!$recipe) die("No recipe");

// Fake comment 1
$comment1 = Comment::create([
    'user_id' => $badUser->id,
    'recipe_id' => $recipe->id,
    'content' => 'Ai có link phim sex không gửi vào đây với',
    'created_at' => now()->subHours(2)
]);

CommentReport::create([
    'comment_id' => $comment1->id,
    'user_id' => $reporter->id,
    'reason' => 'inappropriate',
    'description' => 'Bình luận này chứa từ khóa phim nhạy cảm không phù hợp.',
    'status' => 'pending',
    'created_at' => now()->subMinutes(45)
]);

// Fake comment 2
$comment2 = Comment::create([
    'user_id' => $badUser->id,
    'recipe_id' => $recipe->id,
    'content' => 'Tác giả bài này nấu ăn dở như cứt, đồ con đĩ',
    'created_at' => now()->subHours(1)
]);

CommentReport::create([
    'comment_id' => $comment2->id,
    'user_id' => $reporter->id,
    'reason' => 'offensive',
    'description' => 'Chửi bới thô tục, xúc phạm người khác',
    'status' => 'pending',
    'created_at' => now()->subMinutes(10)
]);

echo "Seeded comment reports matching bad data.";
