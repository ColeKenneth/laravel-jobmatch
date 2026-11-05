<?php
declare(strict_types=1);

namespace App\Traits;

trait HandlesCrud
{
    protected string $successMessage = '';
    protected bool $showSuccessModal = false;
    protected bool $showDeleteModal = false;

    protected ?int $deletingId = null;
    protected ?int $editingId = null;

    protected string $search = '';

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
    
    public function getSuccessMessageProperty(): string
    {
        return $this->successMessage;
    }

    public function getShowSuccessModalProperty(): bool
    {
        return $this->showSuccessModal;
    }

    public function getShowDeleteModalProperty(): bool
    {
        return $this->showDeleteModal;
    }

    public function getDeletingIdProperty(): ?int
    {
        return $this->deletingId;
    }

    public function getEditingIdProperty(): ?int
    {
        return $this->editingId;
    }

    public function getSearchProperty(): string
    {
        return $this->search;
    }

    abstract protected function resetForm() : void;
    abstract protected function payload(array $data) : array;
}
