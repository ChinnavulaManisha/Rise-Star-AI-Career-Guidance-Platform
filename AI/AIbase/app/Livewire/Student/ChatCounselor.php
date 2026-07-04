<?php

namespace App\Livewire\Student;

use Livewire\Component;

use App\Models\AiSession;
use App\Services\AIService;

class ChatCounselor extends Component
{
    public $messages = [];
    public $newMessage = '';

    public function mount()
    {
        $user = auth()->user();
        
        $session = AiSession::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['messages' => []]
        );

        $this->messages = $session->messages ?? [];

        if (empty($this->messages)) {
            $this->messages = [
                [
                    'role' => 'assistant',
                    'content' => "Hello {$user->name}! I am **CareerDesk Bot**, your personal career counselor. I've analyzed your academic grade and status. How can I help you today? Ask me about specific universities, entrance exams, or custom roadmaps!",
                    'created_at' => now()->toDateTimeString()
                ]
            ];
            $session->messages = $this->messages;
            $session->save();
        }
    }

    public function sendMessage(AIService $aiService)
    {
        if (trim($this->newMessage) === '') {
            return;
        }

        $user = auth()->user();
        $session = AiSession::where('user_id', $user->id)->where('status', 'active')->first();

        if (!$session) {
            $session = AiSession::create(['user_id' => $user->id, 'messages' => [], 'status' => 'active']);
        }

        // Add user message immediately and clear input
        $msgText = trim($this->newMessage);
        $this->newMessage = '';

        $userMsg = [
            'role'       => 'user',
            'content'    => $msgText,
            'created_at' => now()->toDateTimeString()
        ];
        $this->messages[] = $userMsg;

        // Get AI response
        $aiResponseText = $aiService->getCounselingResponse($user, $msgText, $this->messages);

        $assistantMsg = [
            'role'       => 'assistant',
            'content'    => $aiResponseText,
            'created_at' => now()->toDateTimeString()
        ];
        $this->messages[] = $assistantMsg;

        // Save to DB
        $session->messages = $this->messages;
        $session->save();

        auth()->user()->addXp(5);

        $this->dispatch('message-sent');
    }

    public function sendSuggestion(string $suggestion)
    {
        $this->newMessage = $suggestion;
        $this->sendMessage(app(\App\Services\AIService::class));
    }

    public function clearChat()
    {
        $user = auth()->user();
        $session = AiSession::where('user_id', $user->id)->where('status', 'active')->first();
        if ($session) {
            $session->messages = [];
            $session->save();
        }
        $this->messages = [];
    }

    public function render()
    {
        return view('livewire.student.chat-counselor');
    }
}
