<?php
$projectRef = 'uxkrgbnmvnzunxgkaunt';
$serviceRole = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InV4a3JnYm5tdm56dW54Z2thdW50Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc4MDM2Mjc1MiwiZXhwIjoyMDk1OTM4NzUyfQ.MNXPQcNUWESNjC-3Lflqj-jBB4rNpW-R_TrPFIXCCtI';

$files = glob(__DIR__ . '/public/assets/recipes/*.*');
$chunks = array_chunk($files, 20); // 20 concurrent requests

$success = 0;
$total = count($files);

foreach ($chunks as $idx => $chunk) {
    $mh = curl_multi_init();
    $handles = [];
    
    foreach ($chunk as $file) {
        $fileName = basename($file);
        $baseUrl = "https://{$projectRef}.supabase.co/storage/v1/object/recipes/{$fileName}";
        $mime = mime_content_type($file);
        
        $ch = curl_init($baseUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$serviceRole}",
            "Content-Type: {$mime}"
        ]);
        
        curl_multi_add_handle($mh, $ch);
        $handles[$file] = $ch;
    }
    
    // Execute all queries simultaneously
    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);
    
    foreach ($handles as $file => $ch) {
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code == 200 || $code == 400) { // 400 is duplicate usually
            $success++;
        }
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    curl_multi_close($mh);
    
    echo "Processed chunk " . ($idx + 1) . "/" . count($chunks) . " (Total successful: $success/$total)\n";
}

echo "Done! Uploaded: $success of $total\n";
