<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$t = App\Models\AptitudeTest::where("category","Interest")->first();
echo $t ? "Found: " . $t->title . " | questions: " . count($t->questions??[]) . " | user_id: " . ($t->user_id ?? "NULL") : "NOT FOUND";

