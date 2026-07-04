<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$t = App\Models\AptitudeTest::where("category","Interest")->first();
echo "Interest test ID: " . ($t ? (string)$t->_id : "NOT FOUND") . "\n";
echo "Questions: " . count($t->questions ?? []) . "\n";
// Check if questions are valid
if($t) {
    $ids = collect($t->questions ?? [])->filter()->map(fn($i)=>(string)$i)->values()->toArray();
    echo "Valid IDs: " . count($ids) . "\n";
    if(count($ids) > 0) {
        $q = App\Models\Question::find($ids[0]);
        echo "First Q: " . ($q ? $q->question_text : "NOT FOUND: ".$ids[0]) . "\n";
    }
}

