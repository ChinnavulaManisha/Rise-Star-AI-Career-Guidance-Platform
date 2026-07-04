<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$test = App\Models\AptitudeTest::where("category","Interest")->first();
echo "Test: " . $test->title . "\n";
echo "Questions count: " . count($test->questions ?? []) . "\n";
$ids = $test->questions ?? [];
shuffle($ids);
$sel = array_slice($ids, 0, 10);
echo "Selected 10 IDs:\n";
foreach($sel as $id) {
    $q = App\Models\Question::find($id);
    echo ($q ? $q->question_text : "NOT FOUND: $id") . "\n";
}

