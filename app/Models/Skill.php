<?php

namespace App\Models;

use App\Enum\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'ontology_id',
        'description',
        'icon_url'
    ];

    #[Override]
    protected function casts() : array
    {
        return [
            'category' => Category::class
        ];
    }

    public function jobSkills() : HasMany
    {
        return $this->hasMany(JobSkill::class);
    }

    public function studentSkills() : HasMany
    {
        return $this->hasMany(StudentSkill::class);
    }
}
