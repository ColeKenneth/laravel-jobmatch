<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Enum\UserRole;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\Employer as EmployerModel;
use App\Models\User;
use App\Services\EmployerService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Employer')]
class Employer extends Component
{
    use WithPagination;

    public string $userId = '';
    public string $companyName = '';
    public string $industry = '';
    public string $website = '';
    public string $companyDescription = '';
    public string $logoUrl = '';
    public string $successMessage = '';
    public bool $showSuccessModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingEmployerId = null;
    public ?int $deletingEmployerId = null;
    public string $search = '';

    protected function rules() : array
    {
        return [
            'userId' => ['required', 'exists:users,id', Rule::unique('employers', 'user_id')->ignore($this->editingEmployerId)],
            'companyName' => ['required', 'string', 'max:80'],
            'industry' => ['required', 'string', 'max:80'],
            'website' => ['nullable', 'string', 'max:500'],
            'companyDescription' => ['nullable', 'string'],
            'logoUrl' => ['required', 'string', 'max:500'],
        ];
    }

    public function create(EmployerService $employerService) : void
    {
        $data = $this->validate();

        try {
            $employerService->create([
                'user_id' => $data['userId'],
                'company_name' => $data['companyName'],
                'industry' => $data['industry'],
                'website' => $data['website'] ?: null,
                'company_description' => $data['companyDescription'] ?: null,
                'logo_url' => $data['logoUrl'],
            ]);

            $this->resetForm();
            $this->successMessage = "Employer was successfully created.";
            $this->showSuccessModal = true;
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('userId', $e->getMessage());
        }
    }

    public function closeSuccessModal() : void
    {
        $this->showSuccessModal = false;
    }

    public function edit(int $id, EmployerService $employerService) : void
    {
        $employer = $employerService->getById($id);

        $this->editingEmployerId = $employer->id;
        $this->userId = (string) $employer->user_id;
        $this->companyName = $employer->company_name;
        $this->industry = $employer->industry;
        $this->website = $employer->website ?? '';
        $this->companyDescription = $employer->company_description ?? '';
        $this->logoUrl = $employer->logo_url;

        $this->resetValidation();
    }

    public function update(EmployerService $employerService) : void
    {
        if ($this->editingEmployerId === null) {
            return;
        }

        $data = $this->validate();

        try {
            $employerService->update($this->editingEmployerId, [
                'user_id' => $data['userId'],
                'company_name' => $data['companyName'],
                'industry' => $data['industry'],
                'website' => $data['website'] ?: null,
                'company_description' => $data['companyDescription'] ?: null,
                'logo_url' => $data['logoUrl']
            ]);

            $this->resetForm();
            $this->successMessage = "Employer was updated successfully.";
            $this->showSuccessModal = true;
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('userId', $e->getMessage());
        }
    }

    public function confirmDelete(int $id) : void
    {
        $this->deletingEmployerId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(EmployerService $employerService) : void
    {
        if ($this->deletingEmployerId === null) {
            return;
        }

        $employerService->delete($this->deletingEmployerId);
        $this->closeDeleteModal();
    }

    public function cancelEdit() : void
    {
        $this->resetForm();
    }

    public function closeDeleteModal() : void
    {
        $this->deletingEmployerId = null;
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
            'companyName',
            'industry',
            'website',
            'companyDescription',
            'logoUrl',
            'editingEmployerId'
        ]);

        $this->resetValidation();
    }

    public function render(EmployerService $employerService) : View
    {
        $eligibleUsers = User::query()
            ->where('role', UserRole::EMPLOYER->value)
            ->whereNotIn('id', EmployerModel::query()->select('user_id'))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('livewire.employer', [
            'employers' => $employerService->getAll($this->search ?: null),
            'eligibleUsers' => $eligibleUsers,
        ]);
    }
}
