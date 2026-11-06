<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Enum\EmploymentType;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\Employer as EmployerModel;
use App\Services\JobPostingService;
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Job Posting')]
class JobPosting extends Component
{
    use WithPagination;
    use HandlesCrud;

    public string $employerId = '';
    public string $title = '';
    public string $description = '';
    public string $requirements = '';
    public string $preferredSkills = '';
    public string $minExperienceYears = '';
    public string $minSalary = '';
    public string $maxSalary = '';
    public string $location = '';
    public string $employmentType = '';
    public string $deadline = '';
    public bool $isActive = true;

    protected function rules() : array
    {
        return [
            'employerId' => ['required', 'string', 'exists:employers,id'],
            'title' => ['required', 'string','max:150'],
            'description' => ['required', 'string'],
            'requirements' => ['required', 'string'],
            'preferredSkills' => ['nullable', 'string'],
            'minExperienceYears' => ['required', 'integer', 'min:0', 'max:60'],
            'minSalary' => ['nullable', 'decimal:0,2', 'min:0', 'max:9999999.99'],
            'maxSalary' => ['nullable', 'decimal:0,2', 'min:0', 'max:9999999.99'],
            'location' => ['required', 'string', 'max:100'],
            'employmentType' => ['required', Rule::enum(EmploymentType::class)],
            'deadline' => ['nullable', 'date'],
            'isActive' => ['boolean'],
        ];
    }

    public function create(JobPostingService $jobPostingService) : void
    {
        $data = $this->validate();

        try {
            $jobPostingService->create($this->payload($data));
            $this->resetForm();
            $this->showSuccess("Job posting created successfully.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('form', $e->getMessage());
        }
    }

    public function edit(int $id, JobPostingService $jobPostingService) : void
    {
        $jobPosting = $jobPostingService->getById($id);

        $this->editingId = $jobPosting->id;
        $this->employerId = (string)$jobPosting->employer_id;
        $this->title = $jobPosting->title;
        $this->description = $jobPosting->description;
        $this->requirements = $jobPosting->requirements;
        $this->preferredSkills = $jobPosting->preferred_skills ?? '';
        $this->minExperienceYears = (string)$jobPosting->min_experience_years;
        $this->minSalary = (string)$jobPosting->min_salary;
        $this->maxSalary = (string)$jobPosting->max_salary;
        $this->location = $jobPosting->location;
        $this->employmentType = $jobPosting->employment_type->value;
        $this->deadline = $jobPosting->deadline?->format('Y-m-d\TH:i') ?? '';
        $this->isActive = $jobPosting->is_active;

        $this->resetValidation();
    }

    public function update(JobPostingService $jobPostingService) : void
    {
        if (!$this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $jobPostingService->update($this->editingId, $this->payload($data) + ['is_active' => $data['isActive']]);
            $this->resetForm();
            $this->showSuccess("Job posting was updated successfully.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('form', $e->getMessage());
        }
    }

    public function toggleActive(int $id, bool $isActive, JobPostingService $jobPostingService) : void
    {
        $isActive ? $jobPostingService->close($id) : $jobPostingService->open($id);
    }

    public function delete(JobPostingService $jobPostingService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $jobPostingService->delete($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'employerId',
            'title',
            'description',
            'requirements',
            'preferredSkills',
            'minExperienceYears',
            'minSalary',
            'maxSalary',
            'location',
            'employmentType',
            'deadline',
            'isActive',
            'editingId'
        ]);

        $this->resetValidation();
    }

    protected function payload(array $data): array
    {
        return [
            'employer_id' => $data['employerId'],
            'title' => $data['title'],
            'description' => $data['description'],
            'requirements' => $data['requirements'],
            'preferred_skills' => $data['preferredSkills'] ?: null,
            'min_experience_years' => $data['minExperienceYears'],
            'min_salary' => $data['minSalary'] === '' ? null : $data['minSalary'],
            'max_salary' => $data['maxSalary'] === '' ? null : $data['maxSalary'],
            'location' => $data['location'],
            'employment_type' => $data['employmentType'],
            'deadline' => $data['deadline'] ?: null,
        ];
    }

    public function render(JobPostingService $jobPostingService) : View
    {
        return view('livewire.job-posting', [
            'jobPostings' => $jobPostingService->getAll($this->search ?: null),
            'employers' => EmployerModel::query()->orderBy('company_name')->get(['id', 'company_name']),
            'employmentTypes' => EmploymentType::cases(),
        ]);
    }


}
