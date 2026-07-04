<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Question extends Model
{
    protected $collection = 'questions';

    protected $fillable = [
        'question_text',
        'options',
        'correct_answer',
        'category',
        'difficulty',
        'explanation',
        'image_url', // optional, e.g. for spatial reasoning
    ];

    protected $casts = [
        'options' => 'array',
    ];
}
