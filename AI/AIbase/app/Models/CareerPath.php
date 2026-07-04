<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class CareerPath extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'career_paths';

    protected $fillable = [
        'title', 'category', 'description', 'overview',
        'required_skills', 'languages_known', 'eligibility',
        'roadmap_steps', 'tools', 'projects_to_build',
        'certifications', 'career_growth', 'salary_range',
        'salary_fresher', 'salary_mid', 'salary_senior',
        'demand_level', 'companies_hiring', 'related_careers',
        'future_scope', 'job_roles', 'colleges', 'resources',
    ];

    protected $casts = [
        'required_skills'   => 'array',
        'languages_known'   => 'array',
        'eligibility'       => 'array',
        'roadmap_steps'     => 'array',
        'tools'             => 'array',
        'projects_to_build' => 'array',
        'certifications'    => 'array',
        'career_growth'     => 'array',
        'companies_hiring'  => 'array',
        'related_careers'   => 'array',
        'job_roles'         => 'array',
        'colleges'          => 'array',
        'resources'         => 'array',
    ];
}
