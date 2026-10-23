<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'skills_gained',
        'supervisor',
        'supervisor_email'
    ];

    #[Override]
    protected function casts() : array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function student() : BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
