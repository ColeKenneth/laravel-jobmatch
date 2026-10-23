<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

class JobSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'skill_id',
        'is_required',
        'min_proficiency_level',
        'priority'
    ];

    #[Override]
    protected function casts() : array
    {
        return [
            'is_required' => 'boolean'
        ];
    }

    public function jobPosting() : BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function skill() : BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
