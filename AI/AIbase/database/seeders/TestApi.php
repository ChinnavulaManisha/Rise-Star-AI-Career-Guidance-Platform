<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$key = config("services.gemini.key") ?? env("GEMINI_API_KEY");
$keys = array_filter(array_map("trim", explode(",", $key)));
$apiKey = $keys[array_rand($keys)];

// Test API first
$testPayload = json_encode(["contents"=>[["parts"=>[["text"=>"Say hello"]]]]]);
$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key=$apiKey");
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$testPayload,CURLOPT_HTTPHEADER=>["Content-Type: application/json"],CURLOPT_TIMEOUT=>30]);
$r = curl_exec($ch);
curl_close($ch);
echo "API test: " . substr($r, 0, 200) . "\n";
