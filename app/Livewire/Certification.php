<?php

namespace App\Livewire;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Services\CertificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Certification extends Component
{
    use WithPagination;

    public string $studentId = '';
    public string $name = '';
    public string $issuingAuthority = '';
    public string $dateIssued = '';
    public string $expiryDate = '';
    public string $verificationUrl = '';
    public string $credentialId = '';
    public string $credential_id = '';
    public string $successMessage = '';
    public bool $showSuccessModal = false;
    public ?int $editingCertificationId = null;
    public ?int $deletingCertificationId = null;
    public bool $showDeleteModal = false;
    public string $search = '';

    protected function rules() : array
    {
        return [
            'studentId' => 'required|exists:students,id', Rule::unique('certifications', 'student_id')->ignore($this->editingCertificationId),
            'name' => 'required|string|max:100',
            'issuingAuthority' => 'required|string|max:100',
            'dateIssued' => 'required|date',
            'expiryDate' => 'nullable|date|after:dateIssued',
            'verificationUrl' => 'nullable|string|max:500',
            'credentialId' => 'nullable|string|max:50'
        ];
    }

    public function create(CertificationService $certificationService) : void
    {
        $data = $this->validate();

        try {
            $certificationService->create([
                'student_id' => $data['studentId'],
                'name' => $data['name'],
                'issuing_authority' => $data['issuingAuthority'],
                'date_issued' => $data['dateIssued'],
                'expiry_date' => $data['expiryDate'] ?: null,
                'verification_url' => $data['verificationUrl'] ?: null,
                'credential_id' => $data['credentialId'] ?: null
            ]);

            $this->resetForm();
            $this->successMessage = "Certification was successfully created.";
            $this->showSuccessModal = true;
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('studentId', $e->getMessage());
        }
    }

    public function closeSuccessModal() : void
    {
        $this->showSuccessModal = false;
    }

    public function edit(int $id, CertificationService $certificationService) : void
    {
        $certification = $certificationService->getById($id);

        $this->editingCertificationId = $certification->id;
        $this->studentId =  (string) $certification->student_id;
        $this->name = $certification->name;
        $this->dateIssued = $certification->date_issued->format('Y-m-d') ?? '';
        $this->expiryDate = $certification->date_issued?->format('Y-m-d') ?? '';
        $this->verificationUrl = $certification->verification_url ?? '';
        $this->credentialId = $certification->credential_id ?? '';

        $this->resetValidation();
    }

    // TO DO TOMORROW: update(), confirmDelete(), delete(), cancelEdit(), closeDeleteModal(), updatedSearch()

    private function resetForm() : void
    {
        $this->reset([
            'studentId',
            'name',
            'issuingAuthority',
            'dateIssued',
            'expiryDate',
            'verificationUrl',
            'credentialId',
            'editingCertificationId'
        ]);
    }

    public function render() : View
    {
        return view('livewire.certification');
    }
}
