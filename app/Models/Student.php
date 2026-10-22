<?php

namespace App\Models;

use App\Enum\YearLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id_number',
        'program',
        'year_level',
        'gwa',
        'graduation_date',
        'resume_file_path',
        'resume_text'
    ];

    protected function casts() : array
    {
        return [
            'year_level' => YearLevel::class,
            'gwa' => 'decimal:2',
            'graduation_date' => 'date'
        ];
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
