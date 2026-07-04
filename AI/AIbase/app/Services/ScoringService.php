<?php

namespace App\Services;

class ScoringService
{
    /**
     * Calculate score for a test attempt
     *
     * @param \App\Models\AptitudeTest $test
     * @param array $userAnswers [question_id => selected_option]
     * @return array
     */
    public function calculateScore($test, array $userAnswers)
    {
        $questions = \App\Models\Question::whereIn('_id', $test->questions)->get();
        
        $totalQuestions = $questions->count();
        $correctCount = 0;
        $categoryScores = [];

        foreach ($questions as $question) {
            $cat = $question->category;
            if (!isset($categoryScores[$cat])) {
                $categoryScores[$cat] = ['total' => 0, 'correct' => 0];
            }
            $categoryScores[$cat]['total']++;

            $userAnswer = $userAnswers[$question->id] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $correctCount++;
                $categoryScores[$cat]['correct']++;
            }
        }

        $percentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

        return [
            'score' => $correctCount,
            'total' => $totalQuestions,
            'percentage' => $percentage,
            'category_scores' => $categoryScores,
        ];
    }
}
