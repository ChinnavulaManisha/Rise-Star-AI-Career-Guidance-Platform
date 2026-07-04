<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get all Career Interest question IDs
$ids = App\Models\Question::where("category", "Career Interest")->pluck("_id")->map(fn($id) => (string)$id)->toArray();

// Remove old interest test
App\Models\AptitudeTest::where("category", "Interest")->delete();

App\Models\AptitudeTest::create([
    "title" => "Career Interest Discovery",
    "category" => "Interest",
    "description" => "10 random questions to discover your career interests and personality. Takes 5 minutes.",
    "duration_minutes" => 10,
    "is_active" => true,
    "questions" => $ids,
    "user_id" => null,
]);
echo "Interest test created with " . count($ids) . " questions.";

