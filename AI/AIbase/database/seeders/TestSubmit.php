<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate what submit does for Interest test
$t = App\Models\AptitudeTest::where("category","Interest")->first();
echo "Test: " . $t->title . " | ID: " . (string)$t->_id . "\n";

// Simulate session IDs
$allIds = collect($t->questions)->filter()->map(fn($i)=>(string)$i)->values()->toArray();
shuffle($allIds);
$sel = array_slice($allIds, 0, 10);
echo "Selected 10 IDs, first: " . $sel[0] . "\n";

// Simulate scoring
$questions = App\Models\Question::whereIn("_id", $sel)->get();
echo "Questions found: " . $questions->count() . "\n";
echo "First Q: " . $questions->first()->question_text . "\n";
echo "All OK - submit should work";

