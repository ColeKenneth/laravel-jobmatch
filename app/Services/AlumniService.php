<?php

namespace App\Services;

use App\Enum\UserRole;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\Alumni;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

final class AlumniService
{
    public function create(array $data) : Alumni
    {
        $user = User::findOrFail($data['user_id']);

        if ($user->role !== UserRole::ALUMNI) {
            throw new BusinessRuleException("User must have the alumni role.");
        }

        if (Alumni::where('user_id', $user->id)->exists()) {
            throw new AlreadyExistsException("A user already exists for this alumnus.");
        }

        $attributes = [
            'user_id' => $user->id,
            'program' => trim($data['program']),
            'graduation_date' => $data['graduation_date'],
            'current_employer' => isset($data['current_employer']) ? trim($data['current_employer']) : null,
            'current_position' => isset($data['current_position']) ? trim($data['current_position']) : null
        ];

        $alumni = Alumni::create($attributes);

        Log::info("Created alumni profile", ['id' => $alumni->id, 'user_id' => $user->id]);

        return $alumni;
    }

    public function update(int $id, array $data) : Alumni
    {
        $alumni = Alumni::findOrFail($id);

        if (!empty($data['user_id']) && (int)$data['user_id'] !== (int)$alumni->user_id) {
            throw new BusinessRuleException("Cannot reassign alumni profile to a different user.");
        }

        $alumni->fill([
            'program' => trim($data['program']),
            'graduation_date' => $data['graduation_date'],
            'current_employer' => isset($data['current_employer']) ? trim($data['current_employer']) : null,
            'current_position' => isset($data['current_position']) ? trim($data['current_position']) : null
        ]);

        $alumni->save();

        Log::info("Updated alumni profile", ['id' => $alumni->id]);

        return $alumni;
    }

    public function delete(int $id) : void
    {
        $alumni = Alumni::findOrFail($id);
        $alumni->delete();

        Log::info("Deleted alumni profile", ['id' => $id]);
    }

    public function getById(int $id) : Alumni
    {
        return Alumni::with('user')->findOrFail($id);
    }

    public function getAll(?string $search = null): LengthAwarePaginator
    {
        return Alumni::query()
            ->with('user')
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                    ->orWhere('program', 'like', "%{$search}%")
                    ->orWhere('current_employer', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);
    }
}
