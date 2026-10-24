<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'skill_id',
        'proficiency_level',
        'years_of_experience'
    ];

    public function student() : BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function skill() : BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
