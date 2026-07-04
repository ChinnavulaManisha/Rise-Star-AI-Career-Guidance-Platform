<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$questions = App\Models\Question::whereIn("category", ["Logical Reasoning","Numerical Ability","Verbal Ability","Personality & Interest"])->get();
echo "Aptitude questions: " . $questions->count() . "\n";
$ids = $questions->map(fn($q) => (string)$q->_id)->toArray();

// Verify
$q = App\Models\Question::find($ids[0]);
echo "Verify: " . ($q ? $q->question_text : "FAIL") . "\n";

// Update all user adaptive tests too
$userTests = App\Models\AptitudeTest::where("category","Adaptive")->get();
foreach($userTests as $t) {
    shuffle($ids);
    $t->questions = array_slice($ids, 0, 15);
    $t->save();
}
echo "Fixed " . $userTests->count() . " adaptive tests\n";
echo "Done!";

