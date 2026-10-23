<?php

namespace App\Models;

use App\Enum\PlacementStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

class PlacementRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'job_posting_id',
        'placement_date',
        'placement_status',
        'notes'
    ];

    #[Override]
    protected function casts() : array
    {
        return [
            'placement_date' => 'datetime',
            'placement_status' => PlacementStatus::class
        ];
    }

    public function student() : BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function jobPosting() : BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }
}
