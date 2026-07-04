<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CareerPath;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        // Cache careers for 10 minutes — they rarely change
        $careers = \Illuminate\Support\Facades\Cache::remember('all_careers', 600, function () {
            return CareerPath::orderBy('title', 'asc')->get();
        });
        return view('student.careers.index', compact('careers'));
    }

    public function explore($title)
    {
        $careers = \Illuminate\Support\Facades\Cache::remember('all_careers', 600, function () {
            return CareerPath::orderBy('title', 'asc')->get();
        });
        $career = $careers->firstWhere('title', urldecode($title));
        $autoExpand = $career ? ['category' => $career->category, 'id' => (string)$career->_id] : null;
        return view('student.careers.index', compact('careers', 'autoExpand'));
    }

    public function toggleBookmark($id)
    {
        $user = auth()->user();
        $bookmarks = $user->bookmarked_careers ?? [];

        if (in_array($id, $bookmarks)) {
            $bookmarks = array_values(array_diff($bookmarks, [$id]));
            $status = 'removed';
        } else {
            $bookmarks[] = $id;
            $status = 'added';
        }

        $user->bookmarked_careers = $bookmarks;
        $user->save();

        return back()->with('success', "Career bookmark {$status} successfully!");
    }

    public function explainStep(Request $request, \App\Services\AIService $aiService)
    {
        $step = $request->input('step');
        if (!$step) return response()->json(['explanation' => '']);
        
        try {
            // Cache the AI explanation indefinitely to provide instant responses on subsequent clicks
            $cacheKey = 'course_explain_' . md5(strtolower(trim($step)));
            
            $explanation = \Illuminate\Support\Facades\Cache::rememberForever($cacheKey, function () use ($aiService, $step) {
                return $aiService->getStepExplanation($step);
            });
            
            return response()->json(['explanation' => $explanation]);
        } catch (\Exception $e) {
            return response()->json(['explanation' => 'Could not load details at this time.'], 500);
        }
    }

    public function blueprint()
    {
        $user = auth()->user();

        // 1. Try to find the latest completed attempt that has a custom Focus Topic
        $attempt = \App\Models\TestAttempt::where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->get()
            ->first(function($a) {
                if (empty($a->category_scores)) return false;
                $test = \App\Models\AptitudeTest::find($a->test_id);
                return $test && $test->category === 'Adaptive' && !empty($test->topic);
            });

        // 2. Fall back to latest adaptive aptitude test without topic focus
        if (!$attempt) {
            $attempt = \App\Models\TestAttempt::where('user_id', $user->id)
                ->orderBy('completed_at', 'desc')
                ->get()
                ->first(function($a) {
                    if (empty($a->category_scores)) return false;
                    $test = \App\Models\AptitudeTest::find($a->test_id);
                    return $test && $test->category === 'Adaptive';
                });
        }

        // 3. Absolute fallback to any attempt with category scores
        if (!$attempt) {
            $attempt = \App\Models\TestAttempt::where('user_id', $user->id)
                ->orderBy('completed_at', 'desc')
                ->get()
                ->first(fn($a) => !empty($a->category_scores));
        }

        if (!$attempt) {
            return view('student.blueprint', ['blueprint' => null]);
        }

        $blueprint = $this->buildBlueprint($attempt);

        // Try to get AI insight (non-blocking — falls back gracefully)
        try {
            $aiService = app(\App\Services\AIService::class);
            $blueprint['ai_insight'] = $aiService->getBlueprintInsight(
                $user->name,
                $blueprint['strongest_area'],
                $blueprint['top_careers'][0]['title'] ?? '',
                $blueprint['overall'],
                $blueprint['logical'],
                $blueprint['numerical'],
                $blueprint['verbal']
            );
        } catch (\Exception $e) {
            $blueprint['ai_insight'] = null;
        }

        if (!$user->ai_blueprint) {
            $user->ai_blueprint = ['generated' => true];
            $user->save();
            $leveledUp = $user->addXp(100);
            $user->awardBadge('blueprint_unlocked', 'Architect of the Future', '🗺️');
            if ($leveledUp) session()->flash('gamification_levelup', '🎉 You reached Level ' . $user->level . '!');
            else session()->flash('gamification_xp', '+100 XP for unlocking your Career Blueprint!');
        }

        return view('student.blueprint', compact('blueprint'));
    }

    public function regenerateBlueprint()
    {
        $user = auth()->user();

        // 1. Try to find the latest completed attempt that has a custom Focus Topic
        $attempt = \App\Models\TestAttempt::where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->get()
            ->first(function($a) {
                if (empty($a->category_scores)) return false;
                $test = \App\Models\AptitudeTest::find($a->test_id);
                return $test && $test->category === 'Adaptive' && !empty($test->topic);
            });

        // 2. Fall back to latest adaptive aptitude test without topic focus
        if (!$attempt) {
            $attempt = \App\Models\TestAttempt::where('user_id', $user->id)
                ->orderBy('completed_at', 'desc')
                ->get()
                ->first(function($a) {
                    if (empty($a->category_scores)) return false;
                    $test = \App\Models\AptitudeTest::find($a->test_id);
                    return $test && $test->category === 'Adaptive';
                });
        }

        // 3. Absolute fallback to any attempt with category scores
        if (!$attempt) {
            $attempt = \App\Models\TestAttempt::where('user_id', $user->id)
                ->orderBy('completed_at', 'desc')
                ->get()
                ->first(fn($a) => !empty($a->category_scores));
        }

        if (!$attempt) {
            return redirect()->route('student.blueprint')->with('error', 'Take an aptitude test first to generate your blueprint.');
        }

        // Reset so AI insight regenerates
        $user->ai_blueprint = null;
        $user->save();

        $leveledUp = $user->addXp(50);
        $redirect = redirect()->route('student.blueprint')->with('success', '✨ Blueprint regenerated with AI!');
        if ($leveledUp) $redirect->with('gamification_levelup', '🎉 You reached Level ' . $user->level . '!');
        else $redirect->with('gamification_xp', '+50 XP for updating your Blueprint!');

        return $redirect;
    }

    private function buildBlueprint(\App\Models\TestAttempt $attempt): array
    {
        $categoryScores = $attempt->category_scores ?? [];

        $scores = [];
        foreach ($categoryScores as $cat => $data) {
            $scores[$cat] = $data['total'] > 0 ? round(($data['correct'] / $data['total']) * 100) : 0;
        }

        $logical     = $scores['Logical Reasoning']      ?? null;
        $numerical   = $scores['Numerical Ability']      ?? null;
        $verbal      = $scores['Verbal Ability']         ?? null;
        $personality = $scores['Personality & Interest'] ?? null;
        $overall     = round($attempt->percentage ?? 0);
        $hasRealScores = !empty($categoryScores);

        // Check if this attempt is related to a focused topic quiz
        $test = \App\Models\AptitudeTest::find($attempt->test_id);
        $topic = $test && !empty($test->topic) ? strtolower(trim($test->topic)) : null;

        // Compute interest domain scores from answers or Focus Topic keywords
        $interestDomain = ['Tech'=>0,'Healthcare'=>0,'Business'=>0,'Arts'=>0];

        if ($topic) {
            // Automatically detect interest domain based on the focus topic keywords!
            $techKeywords    = ['java','python','code','tech','ai','software','algorithm','programming','computer','engineer','math','web','html','css','database','sql','cloud','aws','network'];
            $healthKeywords  = ['medical','hospital','medicine','health','biology','doctor','patient','drug','clinical','anatomy','nurse','pharma'];
            $bizKeywords     = ['business','finance','investment','bank','economic','market','money','entrepreneur','cfo','accounting','mba','manager','sales'];
            $artKeywords     = ['art','design','creative','film','music','write','visual','studio','fashion','content','paint','sketch','ui','ux','graphic'];

            $matched = false;
            foreach ($techKeywords   as $kw) { if (str_contains($topic, $kw)) { $interestDomain['Tech'] = 100; $matched = true; break; } }
            foreach ($healthKeywords as $kw) { if (str_contains($topic, $kw)) { $interestDomain['Healthcare'] = 100; $matched = true; break; } }
            foreach ($bizKeywords    as $kw) { if (str_contains($topic, $kw)) { $interestDomain['Business'] = 100; $matched = true; break; } }
            foreach ($artKeywords    as $kw) { if (str_contains($topic, $kw)) { $interestDomain['Arts'] = 100; $matched = true; break; } }

            // If no keyword matches, default to Tech (as the primary tech/analytical default)
            if (!$matched) {
                $interestDomain['Tech'] = 100;
            }
        } else {
            // Check latest interest test attempt for domain preference as a fallback
            $interestAttempt = \App\Models\TestAttempt::where('user_id', auth()->id())
                ->whereHas('test', fn($q) => $q->where('category', 'Interest'))
                ->orderBy('completed_at', 'desc')
                ->first();

            if ($interestAttempt) {
                $answers = $interestAttempt->answers ?? [];
                $techKeywords    = ['coding','tech','ai','software','algorithm','programming','computer','startup','engineer','math'];
                $healthKeywords  = ['medical','hospital','medicine','health','biology','doctor','patient','drug','clinical','anatomy'];
                $bizKeywords     = ['business','finance','investment','bank','economic','market','money','entrepreneur','cfo','accounting'];
                $artKeywords     = ['art','design','creative','film','music','write','visual','studio','fashion','content'];
                foreach ($answers as $qid => $answer) {
                    $parts = explode(', ', strtolower($answer));
                    foreach ($parts as $a) {
                        $a = trim($a);
                        foreach ($techKeywords   as $kw) { if (str_contains($a, $kw)) { $interestDomain['Tech']++;       break; } }
                        foreach ($healthKeywords as $kw) { if (str_contains($a, $kw)) { $interestDomain['Healthcare']++; break; } }
                        foreach ($bizKeywords    as $kw) { if (str_contains($a, $kw)) { $interestDomain['Business']++;   break; } }
                        foreach ($artKeywords    as $kw) { if (str_contains($a, $kw)) { $interestDomain['Arts']++;       break; } }
                    }
                }
                $total = max(1, array_sum($interestDomain));
                foreach ($interestDomain as $k => $v) $interestDomain[$k] = round(($v / $total) * 100);
            }
        }

        // Use actual scores if available, otherwise use overall
        $logical     = $logical     ?? ($overall > 0 ? $overall : 0);
        $numerical   = $numerical   ?? ($overall > 0 ? $overall : 0);
        $verbal      = $verbal      ?? ($overall > 0 ? $overall : 0);
        $personality = $personality ?? ($overall > 0 ? $overall : 0);

        // Blend aptitude + interest for domain scores
        $iTech    = $interestDomain['Tech']       ?? 0;
        $iHealth  = $interestDomain['Healthcare'] ?? 0;
        $iBiz     = $interestDomain['Business']   ?? 0;
        $iArts    = $interestDomain['Arts']       ?? 0;

        $careerMap = [
            ['title'=>'Software Developer',    'category'=>'Tech',        'score'=>round($logical*0.4+$numerical*0.3+$iTech*0.3),           'icon'=>'💻','reason'=>'Your logical, numerical and tech interest scores align with software development.'],
            ['title'=>'Full Stack Developer',  'category'=>'Tech',        'score'=>round($logical*0.35+$numerical*0.25+$iTech*0.4),          'icon'=>'🌐','reason'=>'Strong tech interest and analytical skills suit full stack development.'],
            ['title'=>'Data Scientist',        'category'=>'Tech',        'score'=>round($numerical*0.4+$logical*0.3+$iTech*0.3),            'icon'=>'📊','reason'=>'Numerical ability and tech interest are core to data science.'],
            ['title'=>'AI Engineer',           'category'=>'Tech',        'score'=>round($logical*0.45+$numerical*0.25+$iTech*0.3),          'icon'=>'🤖','reason'=>'Strong logical reasoning and tech interest align with AI engineering.'],
            ['title'=>'Cybersecurity Analyst', 'category'=>'Tech',        'score'=>round($logical*0.5+$numerical*0.2+$iTech*0.3),            'icon'=>'🔐','reason'=>'Logical problem-solving and tech interest suit cybersecurity.'],
            ['title'=>'Business Analyst',      'category'=>'Business',    'score'=>round($numerical*0.35+$verbal*0.35+$iBiz*0.3),            'icon'=>'💼','reason'=>'Analytical and verbal skills combined with business interest.'],
            ['title'=>'Financial Analyst',     'category'=>'Business',    'score'=>round($numerical*0.45+$verbal*0.2+$iBiz*0.35),            'icon'=>'📈','reason'=>'Numerical strength and business interest suit financial analysis.'],
            ['title'=>'Investment Banker',     'category'=>'Finance',     'score'=>round($numerical*0.4+$verbal*0.2+$iBiz*0.4),              'icon'=>'🏦','reason'=>'Strong numerical ability and business interest align with investment banking.'],
            ['title'=>'Chartered Accountant',  'category'=>'Finance',     'score'=>round($numerical*0.45+$logical*0.2+$iBiz*0.35),           'icon'=>'📋','reason'=>'Numerical precision and business interest are essential for CA.'],
            ['title'=>'Lawyer',                'category'=>'Law',         'score'=>round($logical*0.4+$verbal*0.4+$iBiz*0.2),               'icon'=>'⚖️','reason'=>'Logical reasoning and verbal ability are the pillars of law.'],
            ['title'=>'Civil Services Officer','category'=>'Law',         'score'=>round($logical*0.3+$verbal*0.3+$numerical*0.2+$iBiz*0.2), 'icon'=>'🏛️','reason'=>'Balanced aptitude suits civil services.'],
            ['title'=>'Lecturer',              'category'=>'Education',   'score'=>round($verbal*0.5+$logical*0.2+($iArts+$iBiz)*0.15),      'icon'=>'🎓','reason'=>'Strong verbal skills and broad interest suit teaching.'],
            ['title'=>'Journalist',            'category'=>'Media',       'score'=>round($verbal*0.5+$iArts*0.3+$iBiz*0.2),                 'icon'=>'📰','reason'=>'Verbal ability and creative interest drive journalism.'],
            ['title'=>'Content Strategist',    'category'=>'Media',       'score'=>round($verbal*0.4+$iArts*0.4+$personality*0.2),           'icon'=>'✍️','reason'=>'Verbal skills and creative interest drive content strategy.'],
            ['title'=>'Graphic Designer',      'category'=>'Arts',        'score'=>round($iArts*0.6+$verbal*0.2+$personality*0.2),           'icon'=>'🎨','reason'=>'Strong creative interest and personality suit design.'],
            ['title'=>'Doctor',                'category'=>'Healthcare',  'score'=>round($iHealth*0.5+$personality*0.3+$numerical*0.2),      'icon'=>'🏥','reason'=>'Healthcare interest and empathy are key for medicine.'],
            ['title'=>'Biotechnologist',       'category'=>'Bio-Sciences','score'=>round($iHealth*0.4+$numerical*0.35+$logical*0.25),        'icon'=>'🧬','reason'=>'Healthcare interest and analytical skills suit biotechnology.'],
            ['title'=>'Mechanical Engineer',   'category'=>'Engineering', 'score'=>round($numerical*0.45+$logical*0.35+$iTech*0.2),          'icon'=>'⚙️','reason'=>'Numerical and logical skills suit mechanical engineering.'],
            ['title'=>'Robotics Engineer',     'category'=>'Engineering', 'score'=>round($logical*0.4+$numerical*0.3+$iTech*0.3),            'icon'=>'🦾','reason'=>'Logical reasoning and tech interest align with robotics.'],
            ['title'=>'UI/UX Designer',        'category'=>'Arts',        'score'=>round($iArts*0.5+$verbal*0.25+$iTech*0.25),               'icon'=>'🖌️','reason'=>'Creative interest and tech awareness suit UI/UX design.'],
        ];

        usort($careerMap, fn($a, $b) => $b['score'] - $a['score']);
        $topCareers = array_slice($careerMap, 0, 5);

        $areaScores = [
            'Technology & Engineering' => round($logical*0.3+$numerical*0.3+$iTech*0.4),
            'Business & Finance'       => round($numerical*0.3+$verbal*0.3+$iBiz*0.4),
            'Law & Civil Services'     => round($logical*0.35+$verbal*0.35+$iBiz*0.3),
            'Media & Education'        => round($verbal*0.4+$iArts*0.35+$personality*0.25),
            'Healthcare & Sciences'    => round($iHealth*0.5+$personality*0.3+$numerical*0.2),
        ];
        arsort($areaScores);
        $strongestArea = array_key_first($areaScores);

        return [
            'overall'         => $overall,
            'logical'         => $logical,
            'numerical'       => $numerical,
            'verbal'          => $verbal,
            'personality'     => $personality,
            'has_real_scores' => $hasRealScores,
            'interest_domain' => $interestDomain,
            'top_careers'     => $topCareers,
            'strongest_area'  => $strongestArea,
            'area_scores'     => $areaScores,
            'attempt_date'    => $attempt->completed_at,
            'focus_topic'     => $topic,
        ];
    }
}
