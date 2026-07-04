<?php

namespace App\Services;

use App\Models\AiSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AIService
{
    /**
     * Retrieve a random API key from a comma-separated list in the environment.
     */
    private function getApiKey()
    {
        // Try config first, then env directly
        $keysStr = config('services.gemini.key') ?: env('GEMINI_API_KEY') ?: '';
        if (!$keysStr) return null;
        $keys = array_filter(array_map('trim', explode(',', $keysStr)));
        return empty($keys) ? null : $keys[array_rand($keys)];
    }

    /**
     * Get a chat completion from Google Gemini.
     */
    public function getCounselingResponse($user, $message, $history = [])
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return "Hello {$user->name}! I am **CareerDesk Bot**, your dedicated career advisor from RiseStar AI. Please configure the GEMINI_API_KEY to enable chat capabilities!";
        }

        $grade = $user->grade ?? '12';
        $targetAudience = in_array(strtolower($grade), ['ug', 'undergraduate', 'bachelor']) 
            ? "university undergraduate students in India" 
            : "secondary school students in grades 11 and 12 in India";

        $systemPrompt = "You are CareerDesk Bot, a friendly AI career counselor from RiseStar AI, serving {$targetAudience}. Student: {$user->name}, Grade: {$grade}. Give career advice that is realistic and encouraging. KEEP RESPONSES VERY BRIEF — max 2 short paragraphs or 3 bullet points. Use simple markdown.";

        $geminiContents = [];
        foreach ($history as $index => $msg) {
            $role = ($msg['role'] === 'assistant') ? 'model' : 'user';

            // Gemini requires the first message to be from 'user', skip leading model messages
            if (empty($geminiContents) && $role === 'model') {
                continue;
            }

            // Avoid consecutive same-role messages (Gemini rejects them)
            if (!empty($geminiContents)) {
                $lastRole = end($geminiContents)['role'];
                if ($lastRole === $role) continue;
            }

            $geminiContents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content'] ?? '']]
            ];
        }

        // Always ensure the current user message is the final entry
        // (It may already be in history, but ensure it's there)
        $lastEntry = !empty($geminiContents) ? end($geminiContents) : null;
        $alreadyHasCurrentMsg = $lastEntry && $lastEntry['role'] === 'user'
            && ($lastEntry['parts'][0]['text'] ?? '') === $message;

        if (!$alreadyHasCurrentMsg) {
            // Remove trailing model entry if present so we can append user msg
            if ($lastEntry && $lastEntry['role'] === 'model') {
                array_pop($geminiContents);
            }
            $geminiContents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];
        }

        // Guard: if still empty, just send the message directly
        if (empty($geminiContents)) {
            $geminiContents = [
                ['role' => 'user', 'parts' => [['text' => $message]]]
            ];
        }

        try {
            $payload = [
                'systemInstruction' => [
                    'parts' => [['text' => $systemPrompt]]
                ],
                'contents' => $geminiContents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", $payload);

            if ($response->failed()) {
                Log::error('Gemini Chat API Error - Status: ' . $response->status() . ' Body: ' . $response->body());
                return $this->getFallbackResponse($user->name, $message);
            }

            $res = $response->json();
            $text = $res['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!$text) {
                Log::error('Gemini Chat: Empty response - ' . json_encode($res));
                return $this->getFallbackResponse($user->name, $message);
            }
            return $text;
            
        } catch (\Exception $e) {
            Log::error('Gemini API Exception in Chat: ' . $e->getMessage());
            return $this->getFallbackResponse($user->name, $message);
        }
    }

    private function getFallbackResponse(string $name, string $message): string
    {
        $msg = strtolower($message);
        if (str_contains($msg, 'software') || str_contains($msg, 'coding') || str_contains($msg, 'programming') || str_contains($msg, 'developer')) {
            return "**Software Development** is a great choice, {$name}! 🚀\n\nStart with Python or Java, master Data Structures & Algorithms, and build 3-5 projects. Practice on LeetCode and apply for internships. Top companies like TCS, Infosys, Google, and Amazon hire freshers with strong DSA skills.\n\n**Salary:** ₹4–8 LPA fresher, ₹15–25 LPA mid-level.";
        }
        if (str_contains($msg, 'data') || str_contains($msg, 'ai') || str_contains($msg, 'machine learning') || str_contains($msg, 'ml')) {
            return "**Data Science & AI** is the hottest field right now, {$name}! 📊\n\nLearn Python, statistics, and ML with scikit-learn. Practice on Kaggle competitions. IBM Data Science and Google ML certifications are highly valued.\n\n**Salary:** ₹5–10 LPA fresher, ₹20–35 LPA senior.";
        }
        if (str_contains($msg, 'doctor') || str_contains($msg, 'medical') || str_contains($msg, 'mbbs') || str_contains($msg, 'neet')) {
            return "**Medicine** is a noble and rewarding career, {$name}! 🏥\n\nClear NEET-UG with a high score, complete 5.5 years MBBS, then specialize with MD/MS. AIIMS, CMC Vellore, and JIPMER are top colleges.\n\n**Salary:** ₹8–15 LPA fresher, ₹30–50 LPA specialist.";
        }
        if (str_contains($msg, 'business') || str_contains($msg, 'mba') || str_contains($msg, 'finance') || str_contains($msg, 'ca')) {
            return "**Business & Finance** offers excellent career paths, {$name}! 💼\n\nPursue CA for accounting, CFA for finance, or MBA from IIMs for management. Financial Analysts, Investment Bankers, and Business Analysts are in high demand.\n\n**Salary:** ₹5–12 LPA fresher, ₹20–40 LPA senior.";
        }
        if (str_contains($msg, 'design') || str_contains($msg, 'creative') || str_contains($msg, 'art') || str_contains($msg, 'ux') || str_contains($msg, 'ui')) {
            return "**Design & Creative Arts** is a growing field, {$name}! 🎨\n\nLearn Figma for UI/UX, Adobe tools for graphic design. Build a strong portfolio on Behance/Dribbble. Google UX Design Certificate is a great start.\n\n**Salary:** ₹3–6 LPA fresher, ₹12–20 LPA senior.";
        }
        if (str_contains($msg, 'career') || str_contains($msg, 'path') || str_contains($msg, 'choose') || str_contains($msg, 'which')) {
            return "Great question, {$name}! 🎯 Here are the top career paths based on demand in India:\n\n• **Tech** — Software Dev, AI/ML, Cybersecurity (High demand)\n• **Finance** — CA, Investment Banking, Financial Analysis\n• **Healthcare** — MBBS, Biotechnology, Pharmacy\n• **Business** — MBA, Marketing, Business Analysis\n\nTake the **Aptitude Test** and **Interest Test** on this platform for personalized recommendations!";
        }
        return "Hello {$name}! 👋 I'm CareerDesk Bot, your AI career counselor.\n\nI can help you with:\n• **Career path selection** based on your interests\n• **Exam preparation** tips (NEET, JEE, CAT, UPSC)\n• **Skill development** roadmaps\n• **Salary insights** for different careers\n\nWhat would you like to know about your career?";
    }

    /**
     * Generate an adaptive quiz using Google Gemini API.
     */
    public function generateQuiz(string $difficultyLevel, int $questionCount = 10, string $topic = "General Aptitude")
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured. Please add GEMINI_API_KEY to your .env file.');
        }

        $prompt = "Generate a multiple-choice quiz about '$topic' with exactly $questionCount questions. 
The difficulty level should be '$difficultyLevel'.
Ensure the questions comprehensively test the following 4 core areas: Logical, Verbal, Technical, and Personality skills.
Provide the output strictly in valid JSON format with the following structure:
[
  {
    \"question_text\": \"The question?\",
    \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
    \"correct_answer\": \"The exact string of the correct option\",
    \"category\": \"Logical\", // MUST be exactly one of: 'Logical', 'Verbal', 'Technical', 'Personality'
    \"difficulty\": \"$difficultyLevel\",
    \"explanation\": \"Very brief 1-sentence explanation.\"
  }
]
CRITICAL: To minimize response time, keep all question texts, options, and explanations extremely short and concise. Do not use complex formatting.
Do not include any markdown formatting like ```json or any other text, just the raw JSON array.";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'responseMimeType' => 'application/json'
            ]
        ]);

        if ($response->failed()) {
            Log::error('Gemini API Error: ' . $response->body());
            throw new \Exception('Failed to generate AI quiz from Gemini.');
        }

        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
        
        // Extract JSON array using regex
        if (preg_match('/\[[\s\S]*\]/', $text, $matches)) {
            $text = $matches[0];
        }
        
        $questions = json_decode(trim($text), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Parse Error: ' . $text);
            throw new \Exception('Invalid JSON format received from AI.');
        }

        return $questions;
    }

    /**
     * Generate a comprehensive Career Blueprint using Gemini based on user history.
     */
    public function generateCareerBlueprint($user)
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured.');
        }

        // Get user's test history
        $attempts = \App\Models\TestAttempt::where('user_id', $user->id)->get();
        $historyData = [];
        foreach ($attempts as $attempt) {
            $historyData[] = "Score: {$attempt->percentage}%. Categories: " . json_encode($attempt->category_scores);
        }
        $historyText = empty($historyData) ? "No tests taken yet. Assume general student profile." : implode(" | ", $historyData);

        $prompt = "You are an expert AI Career Counselor. Based on this student's test history: [$historyText], generate a highly detailed Career Blueprint.
Return STRICTLY valid JSON with this exact structure:
{
  \"personality_prediction\": \"Detailed analysis of their personality traits based on scores.\",
  \"emotional_confidence_analysis\": \"Analysis of their confidence and emotional readiness.\",
  \"skill_gaps\": [\"gap 1\", \"gap 2\"],
  \"career_trends\": \"Analysis of future scope and trends.\",
  \"salary_insights\": \"General salary prediction and market insights for their top careers.\",
  \"recommended_careers\": [
    {
      \"title\": \"Career Title\",
      \"match_score\": 95,
      \"description\": \"Why it matches them.\",
      \"skills_needed\": [\"skill 1\", \"skill 2\"]
    }
  ]
}
Do NOT include markdown formatting or ```json blocks. Return ONLY the raw JSON.";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'responseMimeType' => 'application/json'
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to generate AI Blueprint.');
        }

        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        
        // Extract JSON using regex in case Gemini added conversational text
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            $text = $matches[0];
        }
        
        $blueprint = json_decode(trim($text), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON received for Blueprint. Raw: ' . substr($text, 0, 100));
        }

        return $blueprint;
    }

    /**
     * Dynamically invent 5 high-value careers instantly for the library
     */
    public function generateBulkCareers()
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) return [];
        
        $prompt = "Generate exactly 5 unique, high-demand, modern, and premium career paths. Focus on India market context but global outlook. 
        Return ONLY a JSON array of objects where each object includes:
        'title' (string), 'category' (string - MUST BE EXACTLY ONE OF: 'Tech', 'Business', 'Healthcare', 'Arts'), 'description' (string), 'required_skills' (array of strings), 'roadmap_steps' (array of strings), 'salary_range' (string), 'job_roles' (array of strings), 'colleges' (array of strings).
        Return ONLY the raw JSON array text without backticks or formatting.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => 0.8,
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->failed()) return [];
            
            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            return json_decode(trim($text), true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Generate a short AI career insight for the Blueprint page.
     */
    public function getBlueprintInsight(string $name, string $strongestArea, string $topCareer, int $overall, int $logical, int $numerical, int $verbal): string
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) return '';

        $prompt = "You are a career counselor AI. A student named {$name} scored: Overall {$overall}%, Logical Reasoning {$logical}%, Numerical Ability {$numerical}%, Verbal Ability {$verbal}%. Their strongest domain is {$strongestArea} and their top career match is {$topCareer}. Write a 2-sentence personalized career insight that is encouraging, specific to their scores, and tells them why {$topCareer} is a great fit. Be direct and motivating. No markdown.";

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(15)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 120]
                ]);

            if ($response->failed()) return '';
            return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
        } catch (\Exception $e) {
            return '';
        }
    }
    public function getStepExplanation($step)
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) return "API Key missing.";
        
        $prompt = "Explain the educational degree, certification, or course '$step' in exactly 2 concise, informative sentences, focusing on its role as a prerequisite for a career.";
        
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.5]
            ]);

            if ($response->failed()) return "Could not generate explanation.";
            
            return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? "Explanation unavailable.";
        } catch (\Exception $e) {
            return "Failed to load explanation.";
        }
    }

    /**
     * Generate AI Resume Content based on user profile, test history, and target career.
     */
    public function generateResumeContent($user, $targetCareerTitle, $userDetails = [])
    {
        $apiKey = $this->getApiKey();

        // If no API key or rate limited, use fallback immediately
        if (!$apiKey) {
            return $this->getFallbackResumeContent($user, $targetCareerTitle, $userDetails);
        }

        $attempts = \App\Models\TestAttempt::where('user_id', $user->id)->get();
        $historyData = [];
        foreach ($attempts as $attempt) {
            $historyData[] = "Score: {$attempt->percentage}%. Categories: " . json_encode($attempt->category_scores);
        }
        $historyText = empty($historyData) ? "No tests taken." : implode(" | ", $historyData);

        $collegeName  = $userDetails['college_name'] ?? ($user->school ?? 'University');
        $marks        = $userDetails['marks'] ?? '';
        $userProjects = $userDetails['projects'] ?? [];
        $projectsText = '';
        foreach ($userProjects as $p) {
            $projectsText .= "- {$p['title']}: {$p['description']}\n";
        }

        $prompt = "You are an expert Resume Writer and Career Coach.
Generate a professional resume for a student named {$user->name}.
Education level: {$user->grade}. College/School: {$collegeName}. Marks/CGPA: {$marks}.
Target career: '{$targetCareerTitle}'.
Aptitude test strengths: [$historyText].
User's own projects (include these verbatim in suggested_projects):
{$projectsText}

Return STRICTLY valid JSON with this exact structure:
{
  \"professional_summary\": \"A compelling 3-sentence summary.\",
  \"skills\": {
    \"languages\": [\"Java\", \"Python\"],
    \"frameworks\": [\"React\", \"Node.js\"],
    \"tools\": [\"Git\", \"VS Code\"],
    \"soft_skills\": [\"Problem-Solving\", \"Teamwork\"]
  },
  \"core_competencies\": [\"skill 1\", \"skill 2\", \"skill 3\", \"skill 4\", \"skill 5\", \"skill 6\"],
  \"suggested_projects\": [
    {
      \"title\": \"Project Name\",
      \"tech_stack\": \"React, Node.js, MongoDB\",
      \"github_url\": \"\",
      \"duration\": \"Nov 2025 - Jan 2026\",
      \"description\": \"One sentence overview.\",
      \"bullets\": [\"Achievement bullet 1\", \"Achievement bullet 2\", \"Achievement bullet 3\"]
    }
  ],
  \"training\": [
    {
      \"title\": \"Training Program Name\",
      \"provider\": \"Provider Name\",
      \"duration\": \"Jun 2025 - Jul 2025\",
      \"bullets\": [\"What they learned\", \"Achievement\"]
    }
  ],
  \"certificates\": [
    {
      \"title\": \"Certificate Name\",
      \"provider\": \"NPTEL\",
      \"date\": \"Nov 2025\",
      \"url\": \"\"
    }
  ],
  \"achievements\": [
    {
      \"title\": \"Achievement Title\",
      \"date\": \"Mar 2024\",
      \"description\": \"One sentence description.\"
    }
  ],
  \"education\": [
    {
      \"institution\": \"{$collegeName}\",
      \"degree\": \"Bachelor of Technology - Computer Science\",
      \"location\": \"City, State\",
      \"duration\": \"Aug 2023 - Present\",
      \"score\": \"{$marks}\"
    }
  ],
  \"extracurricular_recommendations\": [\"Activity 1\", \"Activity 2\"]
}
Do NOT include markdown formatting or ```json blocks. Return ONLY the raw JSON.";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'responseMimeType' => 'application/json'
            ]
        ]);

        if ($response->failed()) {
            Log::error('Resume API failed: ' . $response->status() . ' ' . substr($response->body(), 0, 200));
            // Fallback: generate a basic resume structure without AI
            return $this->getFallbackResumeContent($user, $targetCareerTitle, $userDetails);
        }

        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            $text = $matches[0];
        }
        
        $resumeData = json_decode(trim($text), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->getFallbackResumeContent($user, $targetCareerTitle, $userDetails);
        }

        return $resumeData;
    }

    private function getFallbackResumeContent($user, $targetCareerTitle, $userDetails = []): array
    {
        $name = $user->name;
        $college = $userDetails['college_name'] ?? ($user->school ?? 'University');
        $marks = $userDetails['marks'] ?? '';

        $skillMap = [
            'Software Developer'    => ['languages'=>['Java','Python','JavaScript'],'frameworks'=>['Spring Boot','React'],'tools'=>['Git','VS Code','MySQL'],'soft_skills'=>['Problem Solving','Teamwork']],
            'Data Scientist'        => ['languages'=>['Python','R','SQL'],'frameworks'=>['TensorFlow','scikit-learn'],'tools'=>['Jupyter','Tableau','Git'],'soft_skills'=>['Analytical Thinking','Communication']],
            'AI Engineer'           => ['languages'=>['Python','C++'],'frameworks'=>['PyTorch','Hugging Face'],'tools'=>['Docker','Kubernetes','Git'],'soft_skills'=>['Research Mindset','Problem Solving']],
            'Business Analyst'      => ['languages'=>['SQL','Excel VBA'],'frameworks'=>['JIRA','Confluence'],'tools'=>['Tableau','Power BI','Excel'],'soft_skills'=>['Communication','Stakeholder Management']],
            'Doctor'                => ['languages'=>['Medical Terminology'],'frameworks'=>['Clinical Protocols'],'tools'=>['EHR Systems','Diagnostic Tools'],'soft_skills'=>['Empathy','Decision Making']],
        ];

        $skills = $skillMap[$targetCareerTitle] ?? ['languages'=>['Communication','Analysis'],'frameworks'=>['MS Office'],'tools'=>['Excel','PowerPoint'],'soft_skills'=>['Leadership','Teamwork']];

        return [
            'professional_summary' => "{$name} is a motivated student targeting a career as a {$targetCareerTitle}. With strong academic foundations and a passion for continuous learning, they are well-positioned to contribute meaningfully to the field. Their aptitude scores demonstrate strong analytical and problem-solving capabilities.",
            'skills' => $skills,
            'core_competencies' => array_merge($skills['languages'], $skills['soft_skills']),
            'suggested_projects' => array_merge(
                $userDetails['projects'] ?? [],
                [['title'=>"Personal {$targetCareerTitle} Project",'tech_stack'=>implode(', ',$skills['languages']),'github_url'=>'','duration'=>'2025 - Present','description'=>"A hands-on project demonstrating core {$targetCareerTitle} skills.",'bullets'=>['Implemented core functionality','Applied best practices','Documented the solution']]]
            ),
            'training' => [['title'=>"{$targetCareerTitle} Fundamentals",'provider'=>'Online Platform','duration'=>'2024 - 2025','bullets'=>['Completed foundational coursework','Earned completion certificate']]],
            'certificates' => [['title'=>"Introduction to {$targetCareerTitle}",'provider'=>'Coursera','date'=>'2025','url'=>'']],
            'achievements' => [['title'=>'Academic Excellence','date'=>'2024','description'=>'Maintained strong academic performance throughout the program.']],
            'education' => [['institution'=>$college,'degree'=>'Bachelor of Technology','location'=>'India','duration'=>'2022 - Present','score'=>$marks]],
            'extracurricular_recommendations' => ['Join relevant professional clubs','Participate in hackathons or competitions','Contribute to open source projects'],
        ];
    }

    /**
     * Get the next question for the Mock Interview.
     */
    public function getMockInterviewResponse($user, $career, $message, $history, $questionCount, $maxQuestions)
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) return "API Key missing.";

        $systemPrompt = "You are a professional hiring manager conducting a mock interview for the position of '$career'. 
You are interviewing a student named {$user->name} (Grade: {$user->grade}).
Your goal is to ask them one specific, relevant interview question at a time. 
You are currently on question {$questionCount} of {$maxQuestions}. 
Ask an engaging, realistic interview question. Keep it to exactly 1 or 2 sentences max. Do NOT provide feedback yet, just ask the next question.";

        $geminiContents = [];
        foreach ($history as $msg) {
            $role = ($msg['role'] === 'assistant') ? 'model' : 'user';
            // Skip leading model messages
            if (empty($geminiContents) && $role === 'model') continue;
            // Skip consecutive same-role messages
            if (!empty($geminiContents) && end($geminiContents)['role'] === $role) continue;
            $geminiContents[] = ['role' => $role, 'parts' => [['text' => $msg['content'] ?? '']]];
        }
        // Ensure ends with user turn
        if (empty($geminiContents) || end($geminiContents)['role'] !== 'user') {
            $geminiContents[] = ['role' => 'user', 'parts' => [['text' => $message]]];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
                'systemInstruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => $geminiContents,
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 150]
            ]);

            if ($response->failed()) return "Could you elaborate on that?";
            return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? "Could you tell me more?";
        } catch (\Exception $e) {
            return "Could you elaborate on that?";
        }
    }

    /**
     * Generate feedback at the end of the Mock Interview.
     */
    public function generateInterviewFeedback($user, $career, $history)
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) throw new \Exception('Gemini API key not configured.');

        $transcript = [];
        foreach ($history as $msg) {
            $speaker = $msg['role'] === 'assistant' ? 'Interviewer' : 'Student';
            $transcript[] = "{$speaker}: {$msg['content']}";
        }
        $transcriptText = implode("\n", $transcript);

        $prompt = "You are an expert Interview Coach. Below is a transcript of a mock interview between a hiring manager and a student for the role of '{$career}'.
Analyze the student's responses and provide strictly JSON feedback with this structure:
{
  \"overall_score\": 85, // out of 100
  \"strengths\": [\"strength 1\", \"strength 2\"],
  \"areas_to_improve\": [\"area 1\", \"area 2\"],
  \"detailed_feedback\": \"A 3-sentence summary of their performance.\"
}

Transcript:
{$transcriptText}

Return ONLY the raw JSON.";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.5, 'responseMimeType' => 'application/json']
        ]);

        if ($response->failed()) throw new \Exception('Failed to generate feedback.');
        
        $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) $text = $matches[0];
        
        $data = json_decode(trim($text), true);
        if (json_last_error() !== JSON_ERROR_NONE) throw new \Exception('Invalid JSON feedback.');
        
        return $data;
    }
    /**
     * Generate a Skill-Gap Analysis and Course Recommender for a student against a target career.
     */
    public function generateSkillGapAnalysis($user, $targetCareerTitle)
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured.');
        }

        $attempts = \App\Models\TestAttempt::where('user_id', $user->id)->get();
        $historyData = [];
        foreach ($attempts as $attempt) {
            $historyData[] = "Score: {$attempt->percentage}%. Categories: " . json_encode($attempt->category_scores);
        }
        $historyText = empty($historyData) ? "No aptitude tests taken yet. Assume baseline high school/undergrad knowledge." : implode(" | ", $historyData);

        $prompt = "You are an expert Career Coach and Educational Strategist. 
A student named {$user->name} (Grade: {$user->grade}) wants to become a '{$targetCareerTitle}'.
Here is their current aptitude test profile: [$historyText].

Analyze the gap between their current profile and the requirements of becoming a {$targetCareerTitle}.
Return STRICTLY valid JSON with this exact structure:
{
  \"current_level\": \"Beginner/Intermediate/Advanced\",
  \"missing_skills\": [\"List of 3-5 specific technical or soft skills they currently lack\"],
  \"recommended_actions\": [
    {
      \"title\": \"Actionable Step (e.g., Take a beginner Python course)\",
      \"description\": \"Why they should do this and what it solves.\"
    }
  ],
  \"learning_resources\": [
    {
      \"name\": \"Name of a popular platform/course (e.g., CS50 on edX, or a specific YouTube channel)\",
      \"type\": \"Course/Book/Video/Community\"
    }
  ],
  \"overall_advice\": \"A brief, encouraging 2-sentence paragraph summarizing their roadmap.\"
}
Do NOT include markdown formatting or ```json blocks. Return ONLY the raw JSON.";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.6,
                'responseMimeType' => 'application/json'
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to generate Skill-Gap Analysis.');
        }

        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            $text = $matches[0];
        }
        
        $data = json_decode(trim($text), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON received for Skill-Gap Analysis.');
        }

        return $data;
    }

    /**
     * Generate 15 multiple-choice questions for a specific focus topic using Gemini.
     */
    public function generateCustomQuestions(string $topic, int $count = 15): array
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return [];
        }

        $prompt = "Generate exactly {$count} multiple-choice questions related to the topic \"{$topic}\". Each question must be highly relevant to \"{$topic}\" (e.g. testing concepts, syntax, principles, or applications of \"{$topic}\"). Do not mix other unrelated categories, keep all questions strictly focused on \"{$topic}\". Mix difficulty: 5 easy, 5 medium, 5 hard. Return ONLY a valid JSON array. Each object must have: {\"question_text\":\"...\",\"options\":[\"A\",\"B\",\"C\",\"D\"],\"correct_answer\":\"exact string matching one option\",\"category\":\"{$topic}\",\"difficulty\":\"easy|medium|hard\",\"explanation\":\"brief 1-sentence explanation\"}. No markdown, no backticks, just raw JSON array.";

        try {
            $payload = [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'responseMimeType' => 'application/json',
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", $payload);

            if ($response->failed()) {
                Log::error('Gemini generateCustomQuestions API Error: ' . $response->body());
                return [];
            }

            $res = $response->json();
            $text = $res['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            $text = preg_replace('/```json|```/', '', $text);
            $questions = json_decode(trim($text), true);

            return is_array($questions) ? $questions : [];

        } catch (\Exception $e) {
            Log::error('Gemini generateCustomQuestions Exception: ' . $e->getMessage());
            return [];
        }
    }
}

