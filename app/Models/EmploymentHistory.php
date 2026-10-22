<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class EmploymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'alumni_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'is_current',
        'responsibilities'
    ];

    #[Override]
    protected function casts() : array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean'
        ];
    }
}
