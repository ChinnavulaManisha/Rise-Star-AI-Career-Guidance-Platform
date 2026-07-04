<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$attempts = App\Models\TestAttempt::orderBy("completed_at","desc")->take(5)->get();
foreach($attempts as $a) {
    $cats = $a->category_scores ?? [];
    echo "Attempt: " . $a->id . " | pct: " . $a->percentage . "% | cats: " . count($cats) . " | test_id: " . $a->test_id . "\n";
    foreach($cats as $cat => $data) {
        echo "  $cat: " . $data["correct"] . "/" . $data["total"] . "\n";
    }
}

