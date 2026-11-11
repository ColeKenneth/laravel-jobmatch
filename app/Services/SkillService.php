<?php
declare(strict_types=1);

namespace App\Services;

use App\Enum\Category;
use App\Exceptions\AlreadyExistsException;
use App\Models\Skill;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class SkillService
{
    public function create(array $data) : Skill
    {
        $name = trim($data['name']);
        $exists = Skill::where('name', $name)->exists();

        if ($exists) {
            throw new AlreadyExistsException("Skill {$data['name']} already exists.");
        }

        $attributes = [
            'name' => $name,
            'category' => trim($data['category']),
            'ontology_id' => isset($data['ontology_id']) ? trim($data['ontology_id']) : null,
            'icon_url' => isset($data['icon_url']) ? trim($data['icon_url']) : null,
        ];

        $skill = Skill::create($attributes);

        Log::info("Created skill", ['id' => $skill->id]);

        return $skill;
    }

    public function delete(int $id) : void
    {
        $skill = Skill::findOrFail($id);
        $skill->delete();

        Log::info("Deleted skill", ['id' => $id]);
    }

    public function exists(int $id) : bool
    {
        return Skill::where('id', $id)->exists();
    }

    public function getAll(?string $search = null) : LengthAwarePaginator
    {
        return Skill::query()
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(10);
    }

    public function getByCategory(Category $category) : Collection
    {
        return Skill::where('category', $category)
            ->orderByDesc('name')
            ->get();
    }

    public function getById(int $id) : Skill
    {
        return Skill::findOrFail($id);
    }

    public function getByName(string $name) : Skill
    {
        return Skill::where('name', $name)->firstOrFail();
    }

    public function update(int $id, string $name, array $data) : Skill
    {
        $skill = Skill::findOrFail($id);
        $name = trim($data['name']);

        $exists = Skill::where('name', $name)->where('id', '!=', $id)->exists();

        if ($exists) {
            throw new AlreadyExistsException("Skill $name already exists.");
        }

        $skill->fill([
            'name' => trim($data['name']),
            'category' => trim($data['category']),
            'ontology_id' => isset($data['ontology_id']) ? trim($data['ontology_id']) : null,
            'icon_url' => isset($data['icon_url']) ? trim($data['icon_url']) : null,
        ]);

        $skill->save();

        Log::info("Updated skill", ['id' => $id]);

        return $skill;
    }
}
