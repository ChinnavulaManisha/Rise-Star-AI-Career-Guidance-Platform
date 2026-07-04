<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$key = config("services.gemini.key") ?? env("GEMINI_API_KEY");
$keys = array_filter(array_map("trim", explode(",", $key)));
$apiKey = $keys[array_rand($keys)];

$categories = [
    ["name"=>"Logical Reasoning","count"=>15,"desc"=>"series, patterns, blood relations, coding-decoding, syllogisms, analogies, direction sense"],
    ["name"=>"Numerical Ability","count"=>15,"desc"=>"percentages, profit/loss, time-speed-distance, ratios, averages, simple/compound interest, number systems"],
    ["name"=>"Verbal Ability","count"=>15,"desc"=>"synonyms, antonyms, fill in the blanks, sentence correction, idioms, one-word substitution"],
    ["name"=>"Personality & Interest","count"=>15,"desc"=>"career interest, work style, personality traits, problem-solving approach, teamwork, leadership"],
];

$allQuestions = [];

foreach ($categories as $cat) {
    $catName = $cat["name"];
    $catCount = $cat["count"];
    $catDesc = $cat["desc"];
    echo "Generating $catCount questions for $catName...\n";
    
    $prompt = "Generate exactly $catCount multiple-choice aptitude questions for the category \"$catName\" covering: $catDesc. Mix difficulty: 5 easy, 5 medium, 5 hard. Return ONLY a valid JSON array. Each object must have: {\"question_text\":\"...\",\"options\":[\"A\",\"B\",\"C\",\"D\"],\"correct_answer\":\"exact string matching one option\",\"category\":\"$catName\",\"difficulty\":\"easy|medium|hard\",\"explanation\":\"brief 1-sentence explanation\"}. No markdown, no backticks, just raw JSON array.";

    $payload = json_encode([
        "contents" => [["parts" => [["text" => $prompt]]]],
        "generationConfig" => ["temperature" => 0.7, "responseMimeType" => "application/json"]
    ]);

    $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key=$apiKey");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_TIMEOUT => 60,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($response, true);
    $text = $res["candidates"][0]["content"]["parts"][0]["text"] ?? "[]";
    $text = preg_replace("/```json|```/", "", $text);
    $questions = json_decode(trim($text), true);
    
    if (!$questions) {
        echo "FAILED for $catName: " . json_last_error_msg() . "\n";
        continue;
    }
    echo "Got " . count($questions) . " questions.\n";
    $allQuestions = array_merge($allQuestions, $questions);
    sleep(1);
}

echo "Total: " . count($allQuestions) . " questions generated.\n";

App\Models\Question::truncate();
foreach ($allQuestions as $q) {
    App\Models\Question::create([
        "question_text" => $q["question_text"] ?? "",
        "options" => $q["options"] ?? [],
        "correct_answer" => $q["correct_answer"] ?? "",
        "category" => $q["category"] ?? "General",
        "difficulty" => $q["difficulty"] ?? "medium",
        "explanation" => $q["explanation"] ?? "",
    ]);
}

$total = App\Models\Question::count();
echo "Seeded $total questions into DB.\n";

$allIds = App\Models\Question::all()->pluck("_id")->map(fn($id) => (string)$id)->toArray();

App\Models\AptitudeTest::where("user_id", null)->delete();
App\Models\AptitudeTest::create([
    "title" => "General Aptitude Assessment",
    "category" => "General",
    "description" => "60 AI-generated questions across Logical Reasoning, Numerical Ability, Verbal Ability, and Personality. Each attempt shows 15 random questions.",
    "duration_minutes" => 15,
    "is_active" => true,
    "questions" => $allIds,
    "user_id" => null,
]);
echo "Test created with " . count($allIds) . " questions. Done!";
