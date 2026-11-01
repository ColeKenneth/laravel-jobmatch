<?php
declare(strict_types=1);

namespace App\Services;

use App\Enum\UserRole;
use App\Exceptions\BusinessRuleException;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class EmployerService
{
    public function create(array $data): Employer
    {
        $user = User::findOrFail($data['user_id']);

        if ($user->role !== UserRole::EMPLOYER) {
            throw new BusinessRuleException("User must have the employer role.");
        }

        $attributes = [
            'user_id' => $data['user_id'],
            'company_name' => trim($data['company_name']),
            'industry' => trim($data['industry']),
            'website' => isset($data['website']) ? trim($data['website']) : null,
            'company_description' => isset($data['company_description']) ? trim($data['company_description']) : null,
            'logo_url' => trim($data['logo_url'])
        ];

        $employer = Employer::create($attributes);

        Log::info("Created employer profile", ['id' => $employer->id, 'user' => $user->id]);

        return $employer;
    }

    public function update(int $id, array $data): Employer
    {
        $employer = Employer::findOrFail($id);

        if (!empty($data['user_id']) && (int)$data['user_id'] !== (int)$employer->user_id) {
            throw new BusinessRuleException("Cannot reassign employer to a different user.");
        }

        $employer->fill([
            'company_name' => trim($data['company_name']),
            'industry' => trim($data['industry']),
            'website' => isset($data['website']) ? trim($data['website']) : null,
            'company_description' => isset($data['company_description']) ? trim($data['company_description']) : null,
            'logo_url' => trim($data['logo_url'])
        ]);

        $employer->save();

        Log::info("Updated employer profile", ['id' => $employer->id]);

        return $employer;
    }

    public function delete(int $id) : void
    {
        $employer = Employer::findOrFail($id);
        $employer->delete();

        Log::info("Deleted employer", ['id' => $id]);
    }

    public function getById(int $id) : Employer
    {
        return Employer::with('user')->findOrFail($id);
    }


    public function getAll(?string $search = null) : LengthAwarePaginator
    {
        return Employer::query()
            ->with('user')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('industry', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10);
    }


}
