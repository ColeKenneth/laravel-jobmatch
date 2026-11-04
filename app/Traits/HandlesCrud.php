<?php
declare(strict_types=1);

namespace App\Traits;

trait HandlesCrud
{
    public string $successMessage = '';
    public bool $showSuccessModal = false;
    public bool $showDeleteModal = false;

    public ?int $deletingId = null;
    public ?int $editingId = null;

    public string $search = '';

    public function closeSuccessModal() : void
    {
        $this->showSuccessModal = false;
    }

    public function confirmDelete(int $id) : void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal() : void
    {
        $this->deletingId = null;
        $this->showDeleteModal = false;
    }

    public function cancelEdit() : void
    {
        $this->editingId = null;
        $this->resetForm();
    }

    public function updatedSearch() : void
    {
        $this->resetPage();
    }

    public function isEditing() : bool
    {
        return $this->editingId !== null;
    }

    protected function showSuccess(string $message) : void
    {
        $this->successMessage = $message;
        $this->showSuccessModal = true;
    }

    abstract protected function resetForm() : void;
}
