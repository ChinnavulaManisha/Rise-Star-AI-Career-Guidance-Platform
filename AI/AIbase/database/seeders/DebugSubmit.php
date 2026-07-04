<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the adaptive test
$adaptive = App\Models\AptitudeTest::where("category","Adaptive")->first();
echo "Adaptive test: " . ($adaptive ? (string)$adaptive->_id . " | cat: " . $adaptive->category : "NOT FOUND") . "\n";

// Get interest test
$interest = App\Models\AptitudeTest::where("category","Interest")->where("is_active",true)->first();
echo "Interest test: " . ($interest ? (string)$interest->_id : "NOT FOUND") . "\n";

// Check if any user took interest test today
$userId = App\Models\User::first()->id;
$interestId = $interest ? (string)$interest->_id : null;
echo "Interest test ID for check: $interestId\n";

$took = App\Models\TestAttempt::where("user_id",$userId)
    ->where("test_id",$interestId)
    ->where("completed_at",">=",now()->startOfDay())
    ->exists();
echo "Already took interest today: " . ($took ? "YES" : "NO") . "\n";

