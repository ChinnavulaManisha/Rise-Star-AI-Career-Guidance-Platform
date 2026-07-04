<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get real IDs
$questions = App\Models\Question::where("category", "Career Interest")->get();
echo "Found " . $questions->count() . " Career Interest questions\n";

$ids = $questions->map(fn($q) => (string)$q->_id)->toArray();
echo "Sample ID: " . ($ids[0] ?? "none") . "\n";

// Verify one works
$test = App\Models\Question::find($ids[0]);
echo "Verify: " . ($test ? $test->question_text : "NOT FOUND") . "\n";

// Delete and recreate interest test
App\Models\AptitudeTest::where("category", "Interest")->delete();
$newTest = App\Models\AptitudeTest::create([
    "title" => "Career Interest Discovery",
    "category" => "Interest",
    "description" => "10 quick questions to discover which career domain fits your personality and interests.",
    "duration_minutes" => 10,
    "is_active" => true,
    "questions" => $ids,
    "user_id" => null,
]);

// Verify stored
$stored = App\Models\AptitudeTest::find($newTest->id);
echo "Stored questions count: " . count($stored->questions ?? []) . "\n";
$first = $stored->questions[0] ?? null;
$q = $first ? App\Models\Question::find($first) : null;
echo "First question: " . ($q ? $q->question_text : "NOT FOUND: $first") . "\n";
echo "Done!";

