<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable; // MongoDB Auth
use Illuminate\Notifications\Notifiable;

use App\Traits\HasExperience;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasExperience;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'grade',
        'school',
        'dob',
        'profile_photo',
        'xp',
        'level',
        'earned_badges',
        'resume_data',
        'ai_blueprint',
        'bookmarked_careers',
    ];

    /**
     * Default attribute values.
     *
     * @var array
     */
    protected $attributes = [
        'xp' => 0,
        'level' => 1,
        'earned_badges' => [],
        'bookmarked_careers' => [],
        'resume_data' => null,
        'ai_blueprint' => null,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
