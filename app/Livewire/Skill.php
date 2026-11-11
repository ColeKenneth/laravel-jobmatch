<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Enum\Category;
use App\Exceptions\AlreadyExistsException;
use App\Services\SkillService;
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Skills')]
class Skill extends Component
{
    use WithPagination;
    use HandlesCrud;

    public string $name = '';
    public string $category = '';
    public string $ontologyId = '';
    public string $description = '';
    public string $iconUrl = '';

    protected function rules() : array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('skills', 'name')->ignore($this->editingId)],
            'category' => ['required', Rule::enum(Category::class)],
            'ontologyId' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'iconUrl' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function create(SkillService $skillService) : void
    {
        $data = $this->validate();

        try {
            $skillService->create($this->payload($data));
            $this->resetForm();
            $this->showSuccess('Skill was created successfully.');
        } catch (AlreadyExistsException $e) {
            $this->addError('name', $e->getMessage());
        }
    }

    public function edit(int $id, SkillService $skillService) : void
    {
        $skill = $skillService->getById($id);

        $this->editingId = $skill->id;
        $this->name = $skill->name;
        $this->category = $skill->category->value;
        $this->ontologyId = $skill->ontology_id ?? '';
        $this->description = $skill->description ?? '';
        $this->iconUrl = $skill->icon_url ?? '';

        $this->resetValidation();
    }

    public function update(SkillService $skillService) : void
    {
        if (!$this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $skillService->update($this->editingId, $this->name, $this->payload($data));
            $this->resetForm();
            $this->showSuccess('Skill was updated successfully.');
        } catch (AlreadyExistsException $e) {
            $this->addError('name', $e->getMessage());
        }
    }

    public function delete(SkillService $skillService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $skillService->delete($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function resetForm() : void
    {
        $this->reset([
            'name',
            'category',
            'ontologyId',
            'description',
            'iconUrl',
            'editingId',
        ]);

        $this->resetValidation();
    }

    protected function payload(array $data) : array
    {
        return [
            'name' => $data['name'],
            'category' => $data['category'],
            'ontology_id' => $data['ontologyId'] === '' ? null : $data['ontologyId'],
            'description' => $data['description'] === '' ? null : $data['description'],
            'icon_url' => $data['iconUrl'] === '' ? null : $data['iconUrl'],
        ];
    }

    public function render(SkillService $skillService) : View
    {
        return view('livewire.skill', [
            'skills' => $skillService->getAll($this->search ?: null),
            'categories' => Category::cases(),
        ]);
    }
}
