<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Question;
class QuestionSeeder extends Seeder {
    public function run(): void {
        Question::truncate();
        $questions = json_decode(file_get_contents(__DIR__ . "/questions_data.json"), true);
        foreach ($questions as $q) { Question::create($q); }
        echo count($questions) . " questions seeded.";
    }
}
