<?php

namespace App\Livewire;

use App\Services\AlumniService;
use Exception;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AlumniList extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, AlumniService $alumniService) : void
    {
        try {
            $alumniService->delete($id);
            session()->flash('message', 'Alumni record deleted successfully.');
        } catch (Exception $e) {
            session()->flash('error', 'Failed to delete record.');
        }
    }

    public function render(AlumniService $alumniService) : View
    {
        return view('livewire.alumni-list', [
            'alumniList' => $alumniService->getAll($this->search),
        ]);
    }
}
