<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AptitudeTest;
use App\Models\Question;

class AptitudeTestSeeder extends Seeder
{
    public function run(): void
    {
        // Get some question IDs to embed
        $logicalQuestions = Question::where('category', 'Logical Reasoning')->pluck('_id')->toArray();
        $numericalQuestions = Question::where('category', 'Numerical Ability')->pluck('_id')->toArray();
        $verbalQuestions = Question::where('category', 'Verbal Ability')->pluck('_id')->toArray();
        $personalityQuestions = Question::where('category', 'Personality & Interest')->pluck('_id')->toArray();

        $tests = [
            [
                'title' => 'General Aptitude Test 1',
                'category' => 'General',
                'description' => 'A basic test covering Logical, Numerical, and Verbal ability.',
                'duration_minutes' => 30,
                'is_active' => true,
                'questions' => array_merge($logicalQuestions, $numericalQuestions, $verbalQuestions),
            ],
            [
                'title' => 'Logical Reasoning Master',
                'category' => 'Logical Reasoning',
                'description' => 'Test your logical thinking skills.',
                'duration_minutes' => 15,
                'is_active' => true,
                'questions' => $logicalQuestions,
            ],
            [
                'title' => 'Personality & Career Interest Assessment',
                'category' => 'Personality & Interest',
                'description' => 'Discover your workplace personality archetype and career matches.',
                'duration_minutes' => 10,
                'is_active' => true,
                'questions' => $personalityQuestions,
            ]
        ];

        foreach ($tests as $test) {
            AptitudeTest::create($test);
        }
    }
}
