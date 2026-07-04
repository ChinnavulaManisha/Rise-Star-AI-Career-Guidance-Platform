<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TestAttempt;
use App\Models\CareerPath;
use App\Models\AptitudeTest;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        // Profile completion
        $profileFields = ['name', 'email', 'grade', 'school', 'dob'];
        $filledFields = collect($profileFields)->filter(fn($f) => !empty($user->$f))->count();
        $profileCompletion = round(($filledFields / count($profileFields)) * 100);

        // Only fetch last 5 attempts — limit DB load
        $attempts = TestAttempt::where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

        // Cache career recommendations per user for 5 min
        $cacheKey = 'dashboard_recs_' . $user->id;
        $recommendations = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($attempts) {
            if ($attempts->isEmpty()) return collect();
            $catScores = $attempts->first()->category_scores ?? [];
            arsort($catScores);
            $best = key($catScores);
            $cat = in_array($best, ['Logical Reasoning','Numerical Ability']) ? 'Tech' : 'Business';
            return CareerPath::where('category', $cat)->take(3)->get();
        });

        $stats = [
            'tests_taken'        => $attempts->count(),
            'saved_careers'      => 0,
            'profile_completion' => $profileCompletion,
        ];

        return view('student.dashboard', compact('stats', 'attempts', 'recommendations', 'profileCompletion'));
    }
}
