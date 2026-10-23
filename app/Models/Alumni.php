<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Alumni extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program',
        'graduation_date',
        'current_employer',
        'current_position',
    ];

    protected $appends = [
        'graduation_year',
    ];

    #[Override]
    protected function casts() : array
    {
        return [
            'graduation_date' => 'date',
        ];
    }

    public function graduationYear() : Attribute
    {
        return Attribute::get(fn () => $this->graduation_date->year);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employmentHistories() : HasMany
    {
        return $this->hasMany(EmploymentHistory::class);
    }

    public function skills() : HasMany
    {
        return $this->hasMany(Skill::class);
    }
}
