<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ids = App\Models\Question::where("category","Career Interest")->get()->map(fn($q)=>(string)$q->_id)->toArray();
echo "Interest questions: ".count($ids)."\n";

App\Models\AptitudeTest::where("category","Interest")->delete();
App\Models\AptitudeTest::create([
    "title"=>"Career Interest Discovery",
    "category"=>"Interest",
    "description"=>"10 quick questions to discover which career domain fits your personality and interests. Takes 5 minutes.",
    "duration_minutes"=>10,
    "is_active"=>true,
    "questions"=>$ids,
    "user_id"=>null,
]);
echo "Created! Total interest tests: ".App\Models\AptitudeTest::where("category","Interest")->count();

