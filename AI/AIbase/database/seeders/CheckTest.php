<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$testId = "6a145a25e436316eda0f5467";
$test = App\Models\AptitudeTest::find($testId);
if(!$test) { echo "Test not found\n"; exit; }
echo "Test: " . $test->title . " | category: " . $test->category . "\n";
$qids = $test->questions ?? [];
echo "Question IDs count: " . count($qids) . "\n";
if(count($qids) > 0) {
    $q = App\Models\Question::find($qids[0]);
    echo "First Q: " . ($q ? $q->question_text . " | cat: " . $q->category : "NOT FOUND: " . $qids[0]) . "\n";
}

