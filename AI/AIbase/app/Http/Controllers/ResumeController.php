<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIService;
use App\Models\CareerPath;

class ResumeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // We need them to have at least explored some careers or have an AI Blueprint to know what resume to build
        // For simplicity, we can let them pick a career they are interested in from a dropdown
        $careers = CareerPath::orderBy('title', 'asc')->get();
        
        return view('student.resume.index', [
            'user' => $user,
            'resumeData' => $user->resume_data,
            'careers' => $careers,
        ]);
    }

    public function generate(Request $request, AIService $aiService)
    {
        $request->validate([
            'career_title'   => 'required|string',
            'phone'          => 'nullable|string|max:20',
            'linkedin'       => 'nullable|string|max:255',
            'github'         => 'nullable|string|max:255',
            'college_name'   => 'nullable|string|max:255',
            'marks'          => 'nullable|string|max:50',
            'school_10'      => 'nullable|string|max:255',
            'marks_10'       => 'nullable|string|max:50',
            'year_10'        => 'nullable|string|max:50',
            'school_inter'   => 'nullable|string|max:255',
            'marks_inter'    => 'nullable|string|max:50',
            'year_inter'     => 'nullable|string|max:50',
            'year_college'   => 'nullable|string|max:50',
            'training'       => 'nullable|array',
            'training.*.title'       => 'nullable|string|max:255',
            'training.*.provider'    => 'nullable|string|max:255',
            'training.*.start_date'  => 'nullable|string|max:50',
            'training.*.end_date'    => 'nullable|string|max:50',
            'training.*.description' => 'nullable|string|max:1000',
            'projects'       => 'nullable|array',
            'projects.*.title'       => 'nullable|string|max:255',
            'projects.*.description' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $targetCareer = $request->input('career_title');

        // Build training array with combined duration string
        $trainingInput = array_filter($request->input('training', []), fn($t) => !empty($t['title']));
        $trainingData = array_map(function($t) {
            $start = trim($t['start_date'] ?? '');
            $end   = trim($t['end_date'] ?? '');
            $duration = $start && $end ? "$start - $end" : ($start ?: $end);
            return [
                'title'       => $t['title'] ?? '',
                'provider'    => $t['provider'] ?? '',
                'duration'    => $duration,
                'description' => $t['description'] ?? '',
                'bullets'     => array_filter(array_map('trim', explode('.', $t['description'] ?? ''))),
            ];
        }, $trainingInput);

        $userDetails = [
            'phone'        => $request->input('phone'),
            'linkedin'     => $request->input('linkedin'),
            'github'       => $request->input('github'),
            'college_name' => $request->input('college_name'),
            'marks'        => $request->input('marks'),
            'year_college' => $request->input('year_college'),
            'school_10'    => $request->input('school_10'),
            'marks_10'     => $request->input('marks_10'),
            'year_10'      => $request->input('year_10'),
            'school_inter' => $request->input('school_inter'),
            'marks_inter'  => $request->input('marks_inter'),
            'year_inter'   => $request->input('year_inter'),
            'training'     => array_values($trainingData),
            'projects'     => array_values(array_filter($request->input('projects', []), fn($p) => !empty($p['title']))),
        ];

        try {
            $resumeContent = $aiService->generateResumeContent($user, $targetCareer, $userDetails);

            // Merge all user-supplied data into resume
            $resumeContent['target_career']  = $targetCareer;
            $resumeContent['generated_at']   = now()->toDateTimeString();
            $resumeContent['phone']          = $userDetails['phone'];
            $resumeContent['linkedin']       = $userDetails['linkedin'];
            $resumeContent['github']         = $userDetails['github'];
            $resumeContent['college_name']   = $userDetails['college_name'];
            $resumeContent['marks']          = $userDetails['marks'];
            $resumeContent['year_college']   = $userDetails['year_college'];
            $resumeContent['school_10']      = $userDetails['school_10'];
            $resumeContent['marks_10']       = $userDetails['marks_10'];
            $resumeContent['year_10']        = $userDetails['year_10'];
            $resumeContent['school_inter']   = $userDetails['school_inter'];
            $resumeContent['marks_inter']    = $userDetails['marks_inter'];
            $resumeContent['year_inter']     = $userDetails['year_inter'];

            // Use user-provided training (overrides AI)
            if (!empty($userDetails['training'])) {
                $resumeContent['training'] = $userDetails['training'];
            }

            // Merge user projects first, then AI suggestions
            $userProjects = $userDetails['projects'];
            $aiProjects   = $resumeContent['suggested_projects'] ?? [];
            $resumeContent['suggested_projects'] = array_merge($userProjects, $aiProjects);

            // Build education array from user inputs
            $resumeContent['education'] = [];
            if (!empty($userDetails['college_name'])) {
                $resumeContent['education'][] = [
                    'institution' => $userDetails['college_name'],
                    'degree'      => $resumeContent['education'][0]['degree'] ?? 'Bachelor of Technology',
                    'location'    => $resumeContent['education'][0]['location'] ?? '',
                    'duration'    => $userDetails['year_college'] ?? 'Present',
                    'score'       => $userDetails['marks'] ?? '',
                ];
            }
            if (!empty($userDetails['school_inter'])) {
                $resumeContent['education'][] = [
                    'institution' => $userDetails['school_inter'],
                    'degree'      => 'Intermediate; Percentage',
                    'location'    => '',
                    'duration'    => $userDetails['year_inter'] ?? '',
                    'score'       => $userDetails['marks_inter'] ?? '',
                ];
            }
            if (!empty($userDetails['school_10'])) {
                $resumeContent['education'][] = [
                    'institution' => $userDetails['school_10'],
                    'degree'      => 'Matriculation; Percentage',
                    'location'    => '',
                    'duration'    => $userDetails['year_10'] ?? '',
                    'score'       => $userDetails['marks_10'] ?? '',
                ];
            }

            $user->resume_data = $resumeContent;
            $user->save();

            $leveledUp = $user->addXp(50);
            $user->awardBadge('resume_builder', 'Career Architect', '📄');

            $redirect = redirect()->route('student.resume.index')->with('success', 'Professional Resume Generated Successfully!');
            if ($leveledUp) {
                $redirect->with('gamification_levelup', '🎉 You reached Level ' . $user->level . '!');
            } else {
                $redirect->with('gamification_xp', '+50 XP for building your Resume!');
            }
            return $redirect;
        } catch (\Exception $e) {
            return back()->with('error', 'Could not generate AI Resume: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone'          => 'nullable|string|max:20',
            'linkedin'       => 'nullable|string|max:255',
            'github'         => 'nullable|string|max:255',
            'college_name'   => 'nullable|string|max:255',
            'marks'          => 'nullable|string|max:50',
            'professional_summary' => 'nullable|string',
            'projects'       => 'nullable|array',
            'projects.*.title'       => 'nullable|string|max:255',
            'projects.*.description' => 'nullable|string|max:1000',
            'core_competencies' => 'nullable|string',
        ]);

        $user = auth()->user();
        $resumeData = $user->resume_data ?? [];

        $resumeData['phone']        = $request->input('phone');
        $resumeData['linkedin']     = $request->input('linkedin');
        $resumeData['github']       = $request->input('github');
        $resumeData['college_name'] = $request->input('college_name');
        $resumeData['marks']        = $request->input('marks');
        $resumeData['professional_summary'] = $request->input('professional_summary', $resumeData['professional_summary'] ?? '');

        $skills = $request->input('core_competencies', '');
        $resumeData['core_competencies'] = array_filter(array_map('trim', explode(',', $skills)));

        $projects = array_filter($request->input('projects', []), fn($p) => !empty($p['title']));
        $resumeData['suggested_projects'] = array_values($projects);

        $user->resume_data = $resumeData;
        $user->save();

        return redirect()->route('student.resume.index')->with('success', 'Resume updated successfully!');
    }
}
