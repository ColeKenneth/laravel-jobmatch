<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'issuing_authority',
        'date_issued',
        'expiry_date',
        'verification_url',
        'credential_id',
    ];

    #[Override]
    protected function casts() : array
    {
        return [
            'date_issued' => 'datetime',
            'expiry_date' => 'datetime',
        ];
    }
}
