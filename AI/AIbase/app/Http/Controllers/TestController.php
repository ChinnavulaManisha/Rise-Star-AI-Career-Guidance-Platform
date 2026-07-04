<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AptitudeTest;
use App\Models\Question;
use App\Models\TestAttempt;
use App\Services\ScoringService;

class TestController extends Controller
{
    public function index()
    {
        $userTests = AptitudeTest::where('is_active', true)
            ->where('user_id', auth()->id())
            ->where('category', 'Adaptive')
            ->get();

        // Interest test is global (no user_id)
        $interestTest = AptitudeTest::where('category', 'Interest')
            ->where('is_active', true)
            ->first();

        // Check if student completed any test with a custom focus topic
        $hasCompletedFocusedTopic = \App\Models\TestAttempt::where('user_id', auth()->id())
            ->whereNotNull('topic')
            ->where('topic', '!=', '')
            ->exists();

        if ($hasCompletedFocusedTopic) {
            $interestTest = null;
        }

        $tests = $userTests;
        return view('student.tests.index', compact('tests', 'interestTest'));
    }

    public function generateAiQuiz(Request $request, \App\Services\AIService $aiService)
    {
        $userId = auth()->id();

        // Delete previous Adaptive tests for this user only (keep Interest test)
        AptitudeTest::where('user_id', $userId)->where('category', 'Adaptive')->delete();

        $topic = trim($request->input('topic', ''));

        if (!empty($topic)) {
            // Generate 15 multiple choice questions related strictly to that topic using Gemini
            $generatedQuestions = $aiService->generateCustomQuestions($topic, 15);

            if (empty($generatedQuestions)) {
                return back()->with('error', 'Failed to generate AI questions for this topic. Please try again or leave it blank.');
            }

            $selectedIds = [];
            foreach ($generatedQuestions as $q) {
                $newQ = \App\Models\Question::create([
                    'question_text'  => $q['question_text'] ?? '',
                    'options'        => $q['options'] ?? [],
                    'correct_answer' => $q['correct_answer'] ?? '',
                    'category'       => $q['category'] ?? $topic,
                    'difficulty'     => $q['difficulty'] ?? 'medium',
                    'explanation'    => $q['explanation'] ?? '',
                ]);
                $selectedIds[] = (string)$newQ->_id;
            }

            $title = "Aptitude Quiz: " . ucfirst($topic);
            $description = "15 AI-generated questions strictly focused on the topic: {$topic}.";
        } else {
            // Pick 15 random analytical questions focusing on Logical Reasoning and Numerical Ability only
            $allIds = \App\Models\Question::whereIn('category', ['Logical Reasoning', 'Numerical Ability'])
                ->get()
                ->map(fn($q) => (string)$q->_id)
                ->toArray();
            
            if (empty($allIds)) {
                return back()->with('error', 'No analytical questions found in the database pool.');
            }

            shuffle($allIds);
            $selectedIds = array_slice($allIds, 0, 15);

            $title = "Analytical Aptitude Quiz";
            $description = "15 randomly selected analytical questions focusing on Logical Reasoning and Numerical Ability.";
        }

        $test = AptitudeTest::create([
            'title'            => $title,
            'category'         => 'Adaptive',
            'description'      => $description,
            'duration_minutes' => 15,
            'is_active'        => true,
            'questions'        => $selectedIds,
            'user_id'          => $userId,
            'topic'            => !empty($topic) ? $topic : null,
        ]);

        return redirect()->route('student.tests.show', $test->id)
            ->with('success', !empty($topic) 
                ? "✨ 15 customized AI questions generated on '{$topic}'!" 
                : "15 analytical questions selected successfully. Good luck!");
    }

    public function show($id)
    {
        $test = AptitudeTest::findOrFail($id);

        $allIds = collect($test->questions ?? [])->filter()->map(fn($i) => (string)$i)->values()->toArray();
        shuffle($allIds);

        $limit = $test->category === 'Interest' ? 10 : 15;
        $selectedIds = array_slice($allIds, 0, $limit);

        $questions = Question::whereIn('_id', $selectedIds)->get()->shuffle();
        session(["active_question_ids_{$id}" => $selectedIds]);

        return view('student.tests.show', compact('test', 'questions'));
    }

    public function submit(Request $request, $id, ScoringService $scoringService)
    {
        try {
        $test = AptitudeTest::findOrFail($id);
        $answers = $request->input('answers', []);

        // For Interest test: checkboxes return arrays — flatten to comma-separated string for storage
        if ($test->category === 'Interest') {
            $flatAnswers = [];
            foreach ($answers as $qid => $val) {
                $flatAnswers[$qid] = is_array($val) ? implode(', ', $val) : $val;
            }
            $answers = $flatAnswers;
        }

        // Use question IDs from form (reliable) or session fallback
        $selectedIds = $request->input('question_ids', []);
        if (empty($selectedIds)) {
            $selectedIds = session("active_question_ids_{$id}", []);
        }
        // Final fallback: use answered question IDs
        $answeredIds = array_keys($answers);
        $questionIds = !empty($selectedIds) ? array_map('strval', $selectedIds) : $answeredIds;

        // Calculate scores manually to ensure category_scores are always saved
        $questions = Question::whereIn('_id', $questionIds)->get();
        $totalQuestions = $questions->count();
        $correctCount = 0;
        $categoryScores = [];

        foreach ($questions as $question) {
            $cat = $question->category;
            if (!isset($categoryScores[$cat])) {
                $categoryScores[$cat] = ['total' => 0, 'correct' => 0];
            }
            $categoryScores[$cat]['total']++;
            $userAnswer = $answers[(string)$question->_id] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $correctCount++;
                $categoryScores[$cat]['correct']++;
            }
        }

        $percentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

        $isFirstAttempt = TestAttempt::where('user_id', auth()->id())->count() === 0;

        $attempt = TestAttempt::create([
            'user_id'         => auth()->id(),
            'test_id'         => $test->id,
            'topic'           => !empty($test->topic) ? $test->topic : null,
            'answers'         => $answers,
            'score'           => $correctCount,
            'percentage'      => $percentage,
            'category_scores' => $categoryScores,
            'time_taken'      => $request->input('time_taken', 0),
            'completed_at'    => now(),
            'question_ids'    => $questionIds,
        ]);

        // Prune to latest 10
        $allAttempts = TestAttempt::where('user_id', auth()->id())
            ->orderBy('completed_at', 'desc')->get();
        if ($allAttempts->count() > 10) {
            foreach ($allAttempts->slice(10) as $excess) $excess->delete();
        }

        if ($isFirstAttempt) auth()->user()->awardBadge('first_test', 'Cognitive Pioneer', '🧠');

        $xpEarned = 50 + ($percentage > 80 ? 20 : 0);
        $leveledUp = auth()->user()->addXp($xpEarned);

        // Invalidate session cache so XP updates in sidebar
        session()->forget(['_u_xp', '_u_level', '_u_tick']);
        session()->forget("active_question_ids_{$id}");

        // After Interest test → redirect to the latest Adaptive test result
        if ($test->category === 'Interest') {
            $latestAdaptive = TestAttempt::where('user_id', auth()->id())
                ->whereIn('test_id', AptitudeTest::where('category', 'Adaptive')->pluck('_id')->map(fn($id) => (string)$id)->toArray())
                ->orderBy('completed_at', 'desc')
                ->first();

            if ($latestAdaptive) {
                session()->flash('gamification_xp', "+{$xpEarned} XP! Your career blueprint is ready.");
                return redirect()->route('student.tests.result', $latestAdaptive->id)
                    ->with('success', '✅ Both tests complete! Here are your career recommendations.');
            }
        }

        // After Adaptive aptitude test → always redirect to Interest test
        if ($test->category === 'Adaptive') {
            $interestTest = AptitudeTest::where('category', 'Interest')->where('is_active', true)->first();
            if ($interestTest) {
                session()->flash('gamification_xp', "+{$xpEarned} XP! Now complete your Career Interest Test.");
                return redirect()->route('student.tests.show', $interestTest->id)
                    ->with('success', '✅ Aptitude done! Answer 10 interest questions for your personalized blueprint.');
            }
        }

        if ($leveledUp) {
            return redirect()->route('student.tests.result', $attempt->id)
                ->with('gamification_levelup', '🎉 You reached Level ' . auth()->user()->level . '!');
        }
        return redirect()->route('student.tests.result', $attempt->id)
            ->with('gamification_xp', "+{$xpEarned} XP Earned!");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Test submit error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return back()->with('error', 'Submission failed: ' . $e->getMessage());
        }
    }

    public function result($attemptId)
    {
        $attempt = TestAttempt::with('test')->findOrFail($attemptId);
        if ($attempt->user_id !== auth()->id()) abort(403);

        // If this is an Interest test result, show the latest Adaptive attempt instead
        $displayAttempt = $attempt;
        if ($attempt->test && $attempt->test->category === 'Interest') {
            $adaptiveAttempt = TestAttempt::where('user_id', auth()->id())
                ->orderBy('completed_at', 'desc')
                ->get()
                ->first(function($a) {
                    $test = \App\Models\AptitudeTest::find($a->test_id);
                    return $test && $test->category === 'Adaptive';
                });
            if ($adaptiveAttempt) {
                $displayAttempt = $adaptiveAttempt;
                $displayAttempt->load('test');
            }
        }

        $answeredIds = array_keys($displayAttempt->answers ?? []);
        if (!empty($answeredIds)) {
            $questionIds = $answeredIds;
        } else {
            $questionIds = $displayAttempt->question_ids ?? [];
            if (count($questionIds) > 15) $questionIds = array_slice($questionIds, 0, 15);
        }

        $questions = Question::whereIn('_id', $questionIds)->get();

        // Get interest test answers for career suggestions
        $interestAttempt = TestAttempt::where('user_id', auth()->id())
            ->orderBy('completed_at', 'desc')
            ->get()
            ->first(function($a) {
                $test = \App\Models\AptitudeTest::find($a->test_id);
                return $test && $test->category === 'Interest';
            });

        $categoryScores = $displayAttempt->category_scores ?? [];
        $suggestions = $this->suggestCareers($categoryScores, $displayAttempt->percentage ?? 0, $interestAttempt);

        return view('student.tests.result', [
            'attempt'   => $displayAttempt,
            'questions' => $questions,
            'suggestions' => $suggestions,
        ]);
    }

    private function suggestCareers(array $categoryScores, float $overallPct, $interestAttempt = null): array
    {
        $scores = [];
        foreach ($categoryScores as $cat => $data) {
            $scores[$cat] = $data['total'] > 0 ? ($data['correct'] / $data['total']) * 100 : 0;
        }

        $logical     = $scores['Logical Reasoning']      ?? max(10, $overallPct);
        $numerical   = $scores['Numerical Ability']      ?? max(10, $overallPct);
        $verbal      = $scores['Verbal Ability']         ?? max(10, $overallPct);
        $personality = $scores['Personality & Interest'] ?? max(10, $overallPct);

        // Detect interest domain from interest test answers
        $iT = $iH = $iB = $iA = $iL = $iE = 0;
        if ($interestAttempt) {
            $techKw   = ['coding','tech','ai','software','algorithm','programming','computer','startup','engineer','math','cto'];
            $healthKw = ['medical','hospital','medicine','health','biology','doctor','patient','drug','clinical','cmo'];
            $bizKw    = ['business','finance','investment','bank','economic','market','money','entrepreneur','cfo'];
            $artKw    = ['art','design','creative','film','music','write','visual','studio','fashion','content','cco'];
            $lawKw    = ['law','legal','judge','court','policy','civil','police','prosecutor'];
            $engKw    = ['mechanical','civil','electrical','robotics','automobile','aerospace'];
            foreach ($interestAttempt->answers ?? [] as $ans) {
                $a = strtolower(is_array($ans) ? implode(' ', $ans) : $ans);
                foreach ($techKw   as $kw) if (str_contains($a,$kw)) { $iT+=10; break; }
                foreach ($healthKw as $kw) if (str_contains($a,$kw)) { $iH+=10; break; }
                foreach ($bizKw    as $kw) if (str_contains($a,$kw)) { $iB+=10; break; }
                foreach ($artKw    as $kw) if (str_contains($a,$kw)) { $iA+=10; break; }
                foreach ($lawKw    as $kw) if (str_contains($a,$kw)) { $iL+=10; break; }
                foreach ($engKw    as $kw) if (str_contains($a,$kw)) { $iE+=10; break; }
            }
            $iT=min(40,$iT); $iH=min(40,$iH); $iB=min(40,$iB);
            $iA=min(40,$iA); $iL=min(40,$iL); $iE=min(40,$iE);
        }

        $all = [
            ['career'=>'Software Developer',    'field'=>'Tech',        'icon'=>'💻', 'match'=>round($logical*0.35+$numerical*0.25+$iT*0.4)],
            ['career'=>'Data Scientist',        'field'=>'Tech',        'icon'=>'📊', 'match'=>round($numerical*0.35+$logical*0.25+$iT*0.4)],
            ['career'=>'AI Engineer',           'field'=>'Tech',        'icon'=>'🤖', 'match'=>round($logical*0.4+$numerical*0.2+$iT*0.4)],
            ['career'=>'Cybersecurity Analyst', 'field'=>'Tech',        'icon'=>'🔐', 'match'=>round($logical*0.4+$numerical*0.2+$iT*0.4)],
            ['career'=>'Full Stack Developer',  'field'=>'Tech',        'icon'=>'🌐', 'match'=>round($logical*0.3+$numerical*0.3+$iT*0.4)],
            ['career'=>'Business Analyst',      'field'=>'Business',    'icon'=>'💼', 'match'=>round($numerical*0.3+$verbal*0.3+$iB*0.4)],
            ['career'=>'Financial Analyst',     'field'=>'Business',    'icon'=>'📈', 'match'=>round($numerical*0.4+$verbal*0.2+$iB*0.4)],
            ['career'=>'Investment Banker',     'field'=>'Finance',     'icon'=>'🏦', 'match'=>round($numerical*0.4+$verbal*0.2+$iB*0.4)],
            ['career'=>'Chartered Accountant',  'field'=>'Finance',     'icon'=>'📋', 'match'=>round($numerical*0.4+$logical*0.2+$iB*0.4)],
            ['career'=>'Lawyer',                'field'=>'Law',         'icon'=>'⚖️', 'match'=>round($logical*0.3+$verbal*0.3+$iL*0.4)],
            ['career'=>'Civil Services Officer','field'=>'Law',         'icon'=>'🏛️', 'match'=>round($logical*0.25+$verbal*0.25+$numerical*0.1+$iL*0.4)],
            ['career'=>'Journalist',            'field'=>'Media',       'icon'=>'📰', 'match'=>round($verbal*0.4+$personality*0.2+$iA*0.4)],
            ['career'=>'Content Strategist',    'field'=>'Media',       'icon'=>'✍️', 'match'=>round($verbal*0.3+$personality*0.3+$iA*0.4)],
            ['career'=>'Graphic Designer',      'field'=>'Arts',        'icon'=>'🎨', 'match'=>round($verbal*0.2+$personality*0.4+$iA*0.4)],
            ['career'=>'Lecturer',              'field'=>'Education',   'icon'=>'🎓', 'match'=>round($verbal*0.4+$personality*0.2+($iA+$iB)*0.2)],
            ['career'=>'Doctor',                'field'=>'Healthcare',  'icon'=>'🏥', 'match'=>round($personality*0.3+$numerical*0.3+$iH*0.4)],
            ['career'=>'Biotechnologist',       'field'=>'Bio-Sciences','icon'=>'🧬', 'match'=>round($numerical*0.3+$logical*0.3+$iH*0.4)],
            ['career'=>'Mechanical Engineer',   'field'=>'Engineering', 'icon'=>'⚙️', 'match'=>round($numerical*0.3+$logical*0.3+$iE*0.4)],
            ['career'=>'Robotics Engineer',     'field'=>'Engineering', 'icon'=>'🦾', 'match'=>round($logical*0.3+$numerical*0.3+$iE*0.4)],
            ['career'=>'UI/UX Designer',        'field'=>'Arts',        'icon'=>'🖌️', 'match'=>round($verbal*0.2+$personality*0.4+$iA*0.4)],
        ];

        // Add reason based on top scores
        foreach ($all as &$c) {
            $c['reason'] = "Based on your aptitude scores" . ($interestAttempt ? " and career interests" : "") . ".";
        }

        usort($all, fn($a,$b) => $b['match'] - $a['match']);
        return array_slice($all, 0, 5);
    }

    public function downloadReport($attemptId, \App\Services\PDFService $pdfService)
    {
        $attempt = TestAttempt::with('test')->findOrFail($attemptId);
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        return $pdfService->generateCareerReport($attempt);
    }
}
