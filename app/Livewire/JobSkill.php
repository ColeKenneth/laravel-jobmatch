<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\Skill;
use App\Services\JobSkillService;
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Job Skills')]
class JobSkill extends Component
{
    use WithPagination;
    use HandlesCrud;

    #[Locked]
    public int $jobPostingId;

    public string $skillId = '';
    public bool $isRequired = true;
    public string $minProficiencyLevel = '';
    public string $priority = '';

    public function mount(int $jobPostingId) : void
    {
        $this->jobPostingId = $jobPostingId;
    }

    protected function rules(): array
    {
        return [
            'skillId' => ['required', 'exists:skills,id'],
            'isRequired' => ['boolean'],
            'minProficiencyLevel' => ['nullable', 'integer', 'min:1', 'max:5'],
            'priority' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function create(JobSkillService $jobSkillService) : void
    {
        $data = $this->validate();

        try {
            $jobSkillService->create($this->jobPostingId, $this->payload($data));
            $this->resetForm();
            $this->showSuccess("Job skill was created successfully.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('skillId', $e->getMessage());
        }
    }

    public function edit(int $id, JobSkillService $jobSkillService) : void
    {
        $jobSkill = $jobSkillService->getById($id);

        $this->editingId = $jobSkill->id;
        $this->skillId = (string)$jobSkill->skill_id;
        $this->isRequired = $jobSkill->is_required;
        $this->minProficiencyLevel = (string)$jobSkill->min_proficiency_level;
        $this->priority = (string)$jobSkill->priority;

        $this->resetValidation();
    }

    public function update(JobSkillService $jobSkillService) : void
    {
        if (!$this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $jobSkillService->updateSkill($this->jobPostingId, $this->payload($data));
            $this->resetForm();
            $this->showSuccess("Job skill was updated successfully.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('skillId', $e->getMessage());
        }
    }

    public function delete(JobSkillService $jobSkillService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $jobSkillService->removeSkill($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'skillId',
            'isRequired',
            'minProficiencyLevel',
            'priority',
            'editingId'
        ]);

        $this->resetValidation();
    }

    protected function payload(array $data): array
    {
        return [
            'skill_id' => (int)$data['skillId'],
            'is_required' => $data['isRequired'],
            'min_proficiency_level' => $data['minProficiencyLevel'] === '' ? null : (int)$data['minProficiencyLevel'],
            'priority' => $data['priority'] === '' ? null : (int)$data['priority'],
        ];
    }

    public function render(JobSkillService $jobSkillService) : View
    {
        return view('livewire.job-skill', [
            'jobSkills' => $jobSkillService->getByJobPosting($this->jobPostingId),
            'skills' => Skill::query()->orderBy('name')->get(['id', 'name'])
        ]);
    }
}
