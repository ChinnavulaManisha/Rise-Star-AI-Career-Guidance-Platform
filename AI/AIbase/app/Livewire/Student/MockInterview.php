<?php

namespace App\Livewire\Student;

use Livewire\Component;
use App\Models\CareerPath;
use App\Services\AIService;

class MockInterview extends Component
{
    public $careers;
    public $selectedCareer = '';
    public $customCareer = '';
    
    public $status = 'setup'; // setup, interviewing, feedback
    
    public $messages = [];
    public $newMessage = '';
    public $questionCount = 0;
    public $maxQuestions = 3;
    
    public $feedbackData = null;

    public function mount()
    {
        $this->careers = CareerPath::select('title')->orderBy('title', 'asc')->get();
    }

    public function startInterview(AIService $aiService)
    {
        if ($this->selectedCareer === 'custom') {
            if (empty(trim($this->customCareer))) return;
            $this->selectedCareer = trim($this->customCareer);
        }

        if (empty($this->selectedCareer)) return;

        $this->status = 'interviewing';
        $this->messages = [];
        $this->questionCount = 0;
        
        $user = auth()->user();
        
        // Initial greeting and first question from AI
        $introText = "Welcome to your Mock Interview for the role of {$this->selectedCareer}. I am your AI Interviewer. We will go through {$this->maxQuestions} questions to test your readiness. Let's begin. Could you please introduce yourself and tell me why you're interested in this field?";
        
        $this->messages[] = [
            'role' => 'assistant',
            'content' => $introText,
        ];
    }

    public function sendMessage(AIService $aiService)
    {
        if (trim($this->newMessage) === '') return;

        $userMsg = [
            'role' => 'user',
            'content' => $this->newMessage,
        ];
        $this->messages[] = $userMsg;
        
        $msgText = $this->newMessage;
        $this->newMessage = '';
        
        $this->questionCount++;
        
        if ($this->questionCount >= $this->maxQuestions) {
            // End of interview, get feedback
            $this->status = 'generating_feedback';
            
            // We use a small hack here to let the UI update to "generating feedback" 
            // then we actually generate it. Livewire's stream or a separate method is best.
            // For now, we will just call it directly.
            $this->generateFeedback($aiService);
            return;
        }

        // Get next question
        $aiResponseText = $aiService->getMockInterviewResponse(auth()->user(), $this->selectedCareer, $msgText, $this->messages, $this->questionCount, $this->maxQuestions);

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $aiResponseText,
        ];
        
        $this->dispatch('message-sent');
    }
    
    public function generateFeedback(AIService $aiService)
    {
        try {
            $this->feedbackData = $aiService->generateInterviewFeedback(auth()->user(), $this->selectedCareer, $this->messages);
            $this->status = 'feedback';
            
            // Gamification: Award XP
            $leveledUp = auth()->user()->addXp(75);
            auth()->user()->awardBadge('mock_interview', 'Silver Tongue', '🎤');
            
            if ($leveledUp) {
                session()->flash('gamification_levelup', '🎉 You reached Level ' . auth()->user()->level . '! Keep up the great work!');
            } else {
                session()->flash('gamification_xp', '+75 XP for completing a Mock Interview!');
            }
            
        } catch (\Exception $e) {
            $this->status = 'setup';
            session()->flash('error', 'Could not generate feedback: ' . $e->getMessage());
        }
    }

    public function resetInterview()
    {
        $this->status = 'setup';
        $this->messages = [];
        $this->questionCount = 0;
        $this->feedbackData = null;
    }

    public function render()
    {
        return view('livewire.student.mock-interview');
    }
}
