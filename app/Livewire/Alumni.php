<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Enum\UserRole;
use App\Exceptions\BusinessRuleException;
use App\Models\User;
use App\Models\Alumni as AlumniModel;
use App\Services\AlumniService;
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Alumni')]
class Alumni extends Component
{
    use WithPagination;
    use HandlesCrud;

    public string $userId = '';
    public string $program = '';
    public string $graduationDate = '';
    public string $currentEmployer = '';
    public string $currentPosition = '';

    protected function rules(): array
    {
        return [
            'userId' => ['required', 'exists:users,id', Rule::unique('alumnis', 'user_id')->ignore($this->editingId)],
            'program' => ['required', 'string', 'max:100'],
            'graduationDate' => ['required', 'date'],
            'currentEmployer' => ['nullable', 'string', 'max:100'],
            'currentPosition' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function save(AlumniService $alumniService) : void
    {
        $data = $this->validate();

        try {
            $alumniService->create($this->payload($data));
            $this->resetForm();
            $this->showSuccess("Alumni created successfully.");
        } catch (BusinessRuleException $e) {
            $this->addError('userId', $e->getMessage());
        }
    }

    public function edit(int $id, AlumniService $alumniService) : void
    {
        $alumni = $alumniService->getById($id);

        $this->editingId = $alumni->id;
        $this->userId = (string)$alumni->user_id;
        $this->program = $alumni->program;
        $this->graduationDate = $alumni->graduation_date->format('Y-m-d');
        $this->currentEmployer = $alumni->current_employer ?? '';
        $this->currentPosition = $alumni->current_position ?? '';

        $this->resetValidation();
    }

    public function update(AlumniService $alumniService) : void
    {
        $data = $this->validate();

        try {
            $alumniService->update($this->editingId, $this->payload($data));
            $this->resetForm();
            $this->showSuccess("Alumni updated successfully.");
        } catch (BusinessRuleException $e) {
            $this->addError('userId', $e->getMessage());
        }
    }

    public function delete(AlumniService $alumniService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $alumniService->delete($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function resetForm() : void
    {
        $this->reset([
            'userId',
            'program',
            'graduationDate',
            'currentEmployer',
            'currentPosition',
            'editingId'
        ]);

        $this->resetValidation();
    }

    protected function payload(array $data) : array
    {
        return [
            'user_id' => $data['userId'],
            'program' => $data['program'],
            'graduation_date' => $data['graduationDate'],
            'current_employer' => $data['currentEmployer'] ?: null,
            'current_position' => $data['currentPosition'] ?: null,
        ];
    }

    public function render(AlumniService $alumniService): View
    {
        $eligibleUsers = User::query()
            ->where('role', UserRole::ALUMNI->value)
            ->whereNotIn('id', AlumniModel::query()->select('user_id'))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('livewire.alumni', [
            'alumni' => $alumniService->getAll($this->search ?: null),
            'eligibleUsers' => $eligibleUsers,
        ]);
    }
}
