<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Exceptions\AlreadyExistsException;
use App\Models\Skill;
use App\Services\StudentSkillService;
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class StudentSkill extends Component
{
    use WithPagination;
    use HandlesCrud;

    #[Locked]
    public int $studentId;

    public string $skillId = '';
    public string $proficiencyLevel = '1';
    public string $yearsOfExperience = '';

    public function mount(int $studentId) : void
    {
        $this->studentId = $studentId;
    }

    protected function rules() : array
    {
        return [
            'skillId' => ['required', 'exists:skills,id'],
            'proficiencyLevel' => ['required', 'integer', 'min:1', 'max:5'],
            'yearsOfExperience' => ['nullable', 'integer', 'min:0', 'max:60'],
        ];
    }

    public function create(StudentSkillService $studentSkillService) : void
    {
        $data = $this->validate();

        try {
            $studentSkillService->addSkill($this->studentId, $this->payload($data));
            $this->resetForm();
            $this->showSuccess('Skill was added successfully.');
        } catch (AlreadyExistsException $e) {
            $this->addError('skillId', $e->getMessage());
        }
    }

    public function edit(int $id, StudentSkillService $studentSkillService) : void
    {
        $studentSkill = $studentSkillService->getById($id);

        $this->editingId = $studentSkill->id;
        $this->skillId = (string) $studentSkill->skill_id;
        $this->proficiencyLevel = (string) $studentSkill->proficiency_level;
        $this->yearsOfExperience = (string) $studentSkill->years_of_experience;

        $this->resetValidation();
    }

    public function update(StudentSkillService $studentSkillService) : void
    {
        if (! $this->isEditing()) {
            return;
        }

        $data = $this->validate();

        $studentSkillService->updateSkill($this->editingId, $this->payload($data));
        $this->resetForm();
        $this->showSuccess('Skill was updated successfully.');
    }

    public function delete(StudentSkillService $studentSkillService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $studentSkillService->removeSkill($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function resetForm() : void
    {
        $this->reset([
            'skillId',
            'proficiencyLevel',
            'yearsOfExperience',
            'editingId',
        ]);

        $this->resetValidation();
    }

    protected function payload(array $data) : array
    {
        return [
            'skill_id' => (int) $data['skillId'],
            'proficiency_level' => (int) $data['proficiencyLevel'],
            'years_of_experience' => $data['yearsOfExperience'] === '' ? null : (int) $data['yearsOfExperience'],
        ];
    }

    public function render(StudentSkillService $studentSkillService) : View
    {
        return view('livewire.student-skill', [
            'studentSkills' => $studentSkillService->getByStudent($this->studentId),
            'skills' => Skill::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
