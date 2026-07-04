<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$keys = ["AIzaSyCbs3_1znPO1Od1C4A6XyGH9HJy8c-D90M","AIzaSyBPcEdP-5w7J7n2KARAbxaNz1Teuai0ox0"];

foreach ($keys as $i => $apiKey) {
    $testPayload = json_encode(["contents"=>[["parts"=>[["text"=>"Say hello in one word"]]]]]);
    $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key=$apiKey");
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$testPayload,CURLOPT_HTTPHEADER=>["Content-Type: application/json"],CURLOPT_TIMEOUT=>15]);
    $r = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($r, true);
    $status = isset($res["error"]) ? "FAIL: ".$res["error"]["message"] : "OK: ".($res["candidates"][0]["content"]["parts"][0]["text"] ?? "?");
    echo "Key $i: " . substr($status, 0, 100) . "\n";
}
