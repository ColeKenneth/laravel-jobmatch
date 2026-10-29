<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Services\AlumniService;
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

    public string $userId = '';
    public string $program = '';
    public string $graduationDate = '';
    public string $currentEmployer = '';
    public string $currentPosition = '';
    public string $successMessage = '';
    public bool $showSuccessModal = false;
    public ?int $editingAlumniId = null;
    public ?int $deletingAlumniId = null;
    public bool $showDeleteModal = false;
    public string $search = '';

    protected function rules(): array
    {
        return [
            'userId' => ['required', 'exists:users,id', Rule::unique('alumnis', 'user_id')->ignore($this->editingAlumniId)],
            'program' => 'required|string|max:100',
            'graduationDate' => 'required|date',
            'currentEmployer' => 'nullable|string|max:100',
            'currentPosition' => 'nullable|string|max:50',
        ];
    }

    public function save(AlumniService $alumniService) : void
    {
        $data = $this->validate();

        $alumniService->create([
            'user_id' => $data['userId'],
            'program' => $data['program'],
            'graduation_date' => $data['graduationDate'],
            'current_employer' => $data['currentEmployer'] ?: null,
            'current_position' => $data['currentPosition'] ?: null,
        ]);

        $this->resetForm();
        $this->successMessage = 'Alumni profile was created successfully';
        $this->showSuccessModal = true;
    }

    public function edit(int $id, AlumniService $alumniService) : void
    {
        $alumni = $alumniService->getById($id);

        $this->editingAlumniId = $alumni->id;
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

        $alumniService->update($this->editingAlumniId, [
            'user_id' => $data['userId'],
            'program' => $data['program'],
            'graduation_date' => $data['graduationDate'],
            'current_employer' => $data['currentEmployer'] ?: null,
            'current_position' => $data['currentPosition'] ?: null,
        ]);

        $this->resetForm();
        $this->successMessage = 'Alumni profile was updated successfully';
        $this->showSuccessModal = true;
    }

    public function confirmDelete(int $id) : void
    {
        $this->deletingAlumniId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(AlumniService $alumniService) : void
    {
        if ($this->deletingAlumniId === null) {
            return;
        }

        $alumniService->delete($this->deletingAlumniId);

        $this->deletingAlumniId = null;
        $this->showDeleteModal = false;
    }

    public function cancelEdit() : void
    {
        $this->resetForm();
    }

    public function closeDeleteModal() : void
    {
        $this->deletingAlumniId = null;
        $this->showDeleteModal = false;
    }

    public function updatedSearch() : void
    {
        $this->resetPage();
    }

    private function resetForm() : void
    {
        $this->reset([
            'userId',
            'program',
            'graduationDate',
            'currentEmployer',
            'currentPosition',
        ]);

        $this->resetValidation();
    }

    public function render(AlumniService $alumniService) : View
    {
        return view('livewire.alumni', [
            'alumni' => $alumniService->getAll($this->search ?: null),
        ]);
    }
}
