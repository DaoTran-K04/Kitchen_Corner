<?php
$ch = curl_init('http://127.0.0.1:8000/api/chatbot');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);

// Test 1: Code a website
echo "--- TEST 1: Xin viết code ---\n";
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => 'Chào bạn, bạn có thể hướng dẫn tôi viết code lập trình một website được không?']));
$res = curl_exec($ch);
$data = json_decode($res, true);
echo $data['reply']['message'] ?? $res;
echo "\n\n";

// Test 2: Nấu cháo
echo "--- TEST 2: Nấu cháo giải cảm ---\n";
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => 'Xin chào, hôm nay tôi hơi mệt, bạn có món cháo nào nấu nhanh giúp giải cảm không?']));
$res = curl_exec($ch);
$data = json_decode($res, true);
echo $data['reply']['message'] ?? $res;
echo "\n\n";

curl_close($ch);
