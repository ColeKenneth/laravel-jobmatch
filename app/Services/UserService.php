<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AlreadyExistsException;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class UserService
{
    public function activate(int $id) : void
    {
        $user = User::findOrFail($id);

        if ($user->is_active) {
            return;
        }

        $user->update(['is_active' => true]);
        Log::info("Activated user", ['id' => $id]);
    }

    public function create(array $data) : User
    {
        $normalizedEmail = strtolower(trim($data['email']));

        $exists = User::where('email', $normalizedEmail)->exists();
        if ($exists) {
            throw new AlreadyExistsException("User {$data['email']} already exists.");
        }

        $attributes = [
            'first_name' => trim($data['first_name']),
            'middle_name' => isset($data['middle_name']) ? trim($data['middle_name']) : null,
            'last_name' => trim($data['last_name']),
            'email' => $normalizedEmail,
            'role' => trim($data['role']),
            'is_active' => $data['is_active'] ?? true,
            'password' => bcrypt(Str::random())
        ];

        $user = User::create($attributes);

        Log::info("Created user", ['id' => $user->id]);

        return $user;
    }

    public function deactivate(int $id) : void
    {
        $user = User::findOrFail($id);

        if (!$user->is_active) {
            return;
        }

        $user->update(['is_active' => false]);
        Log::info("Deactivated user", ['id' => $id]);
    }

    public function delete(int $id) : void
    {
        $user = User::findOrFail($id);
        $user->delete();
    }

    public function emailExists(string $email) : bool
    {
        $normalizedEmail = strtolower(trim($email));

        return User::where('email', $normalizedEmail)->exists();
    }

    public function exists(int $id) : bool
    {
        return User::where('id', $id)->exists();
    }

    public function getAll() : Collection
    {
        return User::orderByDesc('last_name')->get();
    }

    public function getByEmail(string $email) : User
    {
        $normalizedEmail = strtolower(trim($email));

        return User::where('email', $normalizedEmail)->firstOrFail();
    }

    public function getById(int $id) : User
    {
        return User::findOrFail($id);
    }

    public function update(int $id, array $data) : User
    {
        $normalizedEmail = strtolower(trim($data['email']));

        $emailTaken = User::where('id', '!=', $id)
            ->where('email', $normalizedEmail)
            ->exists();

        if ($emailTaken) {
            throw new AlreadyExistsException("User {$data['email']} already exists.");
        }

        $user = $this->getById($id);

        $user->fill([
            'first_name' => trim($data['first_name']),
            'middle_name' => isset($data['middle_name']) ? trim($data['middle_name']) : null,
            'last_name' => trim($data['last_name']),
            'email' => $normalizedEmail,
            'role' => $data['role'],
            'is_active' => $data['is_active'],
        ]);

        $user->save();

        Log::info("Updated user", ['id' => $id]);

        return $user;
    }
}
