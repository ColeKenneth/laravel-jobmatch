<?php

namespace App\Models;

use App\Enum\EmploymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'requirements',
        'preferred_skills',
        'min_experience_years',
        'min_salary',
        'max_salary',
        'location',
        'employment_type',
        'posted_date',
        'deadline',
        'is_active',
    ];

    #[Override]
    protected function casts() : array
    {
        return [
            'min_salary' => 'decimal:2',
            'max_salary' => 'decimal:2',
            'employment_type' => EmploymentType::class,
            'posted_date' => 'datetime',
            'deadline' => 'datetime',
            'is_active' => 'boolean'
        ];
    }

    public function employer() : BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function requiredSkills() : HasMany
    {
        return $this->hasMany(JobSkill::class);
    }

    public function placementRecords() : HasMany
    {
        return $this->hasMany(PlacementRecord::class);
    }
}
