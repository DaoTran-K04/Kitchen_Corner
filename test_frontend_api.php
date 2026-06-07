<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app()->make(App\Http\Controllers\ChatbotController::class);
$request = Illuminate\Http\Request::create('/api/chatbot', 'POST', [
    'message' => 'Xin chào, hôm nay tôi hơi mệt, bạn có món cháo nào nấu nhanh giúp giải cảm không?',
    'history' => []
]);
$response = $controller->chat($request);
echo "Response:\n";
echo $response->getContent();
