<?php

namespace App\Livewire\Student;

use Livewire\Component;
use App\Models\CareerPath;
use App\Services\AIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SkillGap extends Component
{
    public $careers;
    public $selectedCareer = '';
    public $analysisData = null;
    public $isAnalyzing = false;
    public $error = null;

    public function mount()
    {
        // Load all careers for the dropdown
        $this->careers = CareerPath::select('title')->distinct()->orderBy('title')->get();
    }

    public function generateAnalysis(AIService $aiService)
    {
        $this->validate([
            'selectedCareer' => 'required|string',
        ]);

        $this->error = null;
        $this->analysisData = null;

        try {
            $user = Auth::user();
            
            // Generate the analysis using the AIService
            $this->analysisData = $aiService->generateSkillGapAnalysis($user, $this->selectedCareer);
            
            // Gamification Hook: Award XP for seeking knowledge
            if ($this->analysisData) {
                $leveledUp = $user->addXp(60);
                session()->flash('gamification_xp', '+60 XP: Skill-Gap Analyzed!');
                
                if ($leveledUp) {
                    session()->flash('gamification_levelup', 'You reached Level ' . $user->level . '!');
                }
                
                // Add Knowledge Seeker badge if not already earned
                $user->awardBadge('knowledge_seeker', 'Knowledge Seeker', '🧠');
            }

        } catch (\Exception $e) {
            Log::error('Skill Gap Analysis Error: ' . $e->getMessage());
            $this->error = 'We encountered an issue analyzing your skill gap. Please try again later.';
        }
    }

    public function render()
    {
        return view('livewire.student.skill-gap');
    }
}
