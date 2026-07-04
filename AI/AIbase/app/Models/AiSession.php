<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AiSession extends Model
{
    protected $collection = 'ai_sessions';

    protected $fillable = [
        'user_id',
        'messages',
        'summary',
    ];

    protected $casts = [
        'messages' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
