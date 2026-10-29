<div class="mx-auto max-w-7xl space-y-6 p-6">
    <section class="rounded-lg bg-white p-6 shadow">
        <h2 class="mb-6 text-xl font-semibold text-gray-900">
            {{ $editingAlumniId ? 'Update Alumni' : 'Create Alumni' }}
        </h2>

        <form wire:submit="{{ $editingAlumniId ? 'update' : 'save' }}" class="space-y-4">
            @if(!$editingAlumniId)
                <div>
                    <label for="userId" class="mb-1 block text-sm font-medium text-gray-700">User ID</label>
                    <input id="userId" type="number" wire:model="userId" class="w-full rounded-md border border-gray-300 px-3 py-2">
                    @error('')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <label for="program" class="mb-1 block text-sm font-medium text-gray-700">Program</label>
                <input id="program" type="text" class="w-full rounded-md border-gray-300 px-3 py-2">
                @error('program')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </form>
    </section>
</div>
