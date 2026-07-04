<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TestAttempt extends Model
{
    protected $collection = 'test_attempts';

    protected $fillable = [
        'user_id',
        'test_id',
        'topic',
        'answers',
        'score',
        'percentage',
        'category_scores',
        'question_ids',
        'time_taken',
        'completed_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'float',
        'percentage' => 'float',
        'category_scores' => 'array',
        'question_ids' => 'array',
        'time_taken' => 'integer', // in seconds
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(AptitudeTest::class, 'test_id');
    }
}
