<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AptitudeTest extends Model
{
    protected $collection = 'aptitude_tests';

    protected $fillable = [
        'title',
        'category',
        'description',
        'duration_minutes',
        'is_active',
        'questions', // Can be an array of embedded question IDs or full question objects
        'user_id', // For AI generated tests specific to a user
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'questions' => 'array',
        'duration_minutes' => 'integer',
    ];
}
