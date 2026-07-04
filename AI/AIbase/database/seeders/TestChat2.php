<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$k = "AIzaSyBPcEdP-5w7J7n2KARAbxaNz1Teuai0ox0";
$payload = json_encode(["contents"=>[["parts"=>[["text"=>"What career suits a student good at math?"]]]],"generationConfig"=>["maxOutputTokens"=>100]]);
$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key=$k");
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$payload,CURLOPT_HTTPHEADER=>["Content-Type: application/json"],CURLOPT_TIMEOUT=>15]);
$r = curl_exec($ch); curl_close($ch);
echo substr($r, 0, 500);

