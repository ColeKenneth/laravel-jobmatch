<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Enum\UserRole;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Services\UserService;
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Users')]
class User extends Component
{
    use WithPagination;
    use HandlesCrud;

    public string $firstName = '';
    public string $middleName = '';
    public string $lastName = '';
    public string $email = '';
    public string $role = '';
    public bool $isActive = true;

    protected function rules() : array
    {
        return [
            'firstName' => ['required', 'string', 'max:100'],
            'middleName' => ['nullable', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'isActive' => ['boolean'],
        ];
    }

    public function create(UserService $userService) : void
    {
        $data = $this->validate();

        try {
            $userService->create($this->payload($data));
            $this->resetForm();
            $this->showSuccess('User was created successfully.');
        } catch (AlreadyExistsException $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function edit(int $id, UserService $userService) : void
    {
        $user = $userService->getById($id);

        $this->editingId = $user->id;
        $this->firstName = $user->first_name;
        $this->middleName = $user->middle_name ?? '';
        $this->lastName = $user->last_name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->isActive = $user->is_active;

        $this->resetValidation();
    }

    public function update(UserService $userService) : void
    {
        if (! $this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $userService->update($this->editingId, $this->payload($data));
            $this->resetForm();
            $this->showSuccess('User was updated successfully.');
        } catch (AlreadyExistsException $e) {
            $this->addError('email', $e->getMessage());
        } catch (BusinessRuleException $e) {
            $this->addError('form', $e->getMessage());
        }
    }

    public function toggleActive(int $id, bool $isActive, UserService $userService) : void
    {
        if ($id === (int) auth()->id()) {
            $this->addError('form', 'You cannot deactivate your own account.');
            return;
        }

        $isActive ? $userService->deactivate($id) : $userService->activate($id);
    }

    public function delete(UserService $userService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        if ($this->deletingId === (int) auth()->id()) {
            $this->closeDeleteModal();
            $this->addError('form', 'You cannot delete your own account.');
            return;
        }

        $userService->delete($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function resetForm() : void
    {
        $this->reset([
            'firstName',
            'middleName',
            'lastName',
            'email',
            'role',
            'isActive',
            'editingId',
        ]);

        $this->resetValidation();
    }

    protected function payload(array $data) : array
    {
        return [
            'first_name' => $data['firstName'],
            'middle_name' => $data['middleName'] === '' ? null : $data['middleName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $data['isActive'],
        ];
    }

    public function render(UserService $userService) : View
    {
        return view('livewire.user', [
            'users' => $userService->getAll($this->search ?: null),
            'roles' => UserRole::cases(),
        ]);
    }
}
