<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Enum\UserRole;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\Employer as EmployerModel;
use App\Models\User;
use App\Services\EmployerService;
use App\Traits\HandlesCrud;
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
    use HandlesCrud;

    public string $userId = '';
    public string $companyName = '';
    public string $industry = '';
    public string $website = '';
    public string $companyDescription = '';
    public string $logoUrl = '';

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
            $employerService->create($this->payload($data));

            $this->resetForm();
            $this->showSuccess("Employer was successfully created.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('userId', $e->getMessage());
        }
    }

    public function edit(int $id, EmployerService $employerService) : void
    {
        $employer = $employerService->getById($id);

        $this->editingId = $employer->id;
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
        if (!$this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $employerService->update($this->editingId, $this->payload($data));

            $this->resetForm();
            $this->showSuccess("Employer was successfully updated.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('userId', $e->getMessage());
        }
    }

    public function delete(EmployerService $employerService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $employerService->delete($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function resetForm() : void
    {
        $this->reset([
            'userId',
            'companyName',
            'industry',
            'website',
            'companyDescription',
            'logoUrl',
            'editingId'
        ]);

        $this->resetValidation();
    }

    protected function payload(array $data) : array
    {
        return [
            'user_id' => $data['userId'],
            'company_name' => $data['companyName'],
            'industry' => $data['industry'],
            'website' => $data['website'] ?: null,
            'company_description' => $data['companyDescription'] ?: null,
            'logo_url' => $data['logoUrl'],
        ];
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
