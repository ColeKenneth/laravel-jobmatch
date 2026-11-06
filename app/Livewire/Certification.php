<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Services\CertificationService;
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Certification')]
class Certification extends Component
{
    use WithPagination;
    use HandlesCrud;

    public string $studentId = '';
    public string $name = '';
    public string $issuingAuthority = '';
    public string $dateIssued = '';
    public string $expiryDate = '';
    public string $verificationUrl = '';
    public string $credentialId = '';

    protected function rules(): array
    {
        return [
            'studentId' => ['required', 'exists:students,id'],
            'name' => ['required', 'string', 'max:100'],
            'issuingAuthority' => ['required', 'string', 'max:100'],
            'dateIssued' => ['required', 'date'],
            'expiryDate' => ['nullable', 'date', 'after:dateIssued'],
            'verificationUrl' => ['nullable', 'string', 'max:500'],
            'credentialId' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function create(CertificationService $certificationService): void
    {
        $data = $this->validate();

        try {
            $certificationService->create($this->payload($data));
            $this->resetForm();
            $this->showSuccess("Certification created successfully.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('studentId', $e->getMessage());
        }
    }

    public function edit(int $id, CertificationService $certificationService): void
    {
        $certification = $certificationService->getById($id);

        $this->editingId = $certification->id;
        $this->studentId = (string) $certification->student_id;
        $this->name = $certification->name;
        $this->issuingAuthority = $certification->issuing_authority;
        $this->dateIssued = $certification->date_issued->format('Y-m-d');
        $this->expiryDate = $certification->expiry_date?->format('Y-m-d') ?? '';
        $this->verificationUrl = $certification->verification_url ?? '';
        $this->credentialId = $certification->credential_id ?? '';

        $this->resetValidation();
    }

    public function update(CertificationService $certificationService): void
    {
        if (!$this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $certificationService->update($this->editingId, $this->payload($data));
            $this->resetForm();
            $this->showSuccess("Certification updated successfully.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('studentId', $e->getMessage());
        }
    }

    public function delete(CertificationService $certificationService): void
    {
        if ($this->deletingId === null) {
            return;
        }

        $certificationService->delete($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function payload(array $data) : array
    {
        return [
            'student_id' => $data['studentId'],
            'name' => $data['name'],
            'issuing_authority' => $data['issuingAuthority'],
            'date_issued' => $data['dateIssued'],
            'expiry_date' => $data['expiryDate'] ?: null,
            'verification_url' => $data['verificationUrl'] ?: null,
            'credential_id' => $data['credentialId'] ?: null,
        ];
    }

    protected function resetForm(): void
    {
        $this->reset([
            'studentId',
            'name',
            'issuingAuthority',
            'dateIssued',
            'expiryDate',
            'verificationUrl',
            'credentialId',
            'editingId',
        ]);

        $this->resetValidation();
    }

    public function render(CertificationService $certificationService): View
    {
        return view('livewire.certification', [
            'certifications' => $certificationService->getAll($this->search ?: null),
        ]);
    }
}
