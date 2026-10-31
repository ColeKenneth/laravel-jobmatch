<div class="mx-auto max-w-7xl space-y-6 p-6">

    <section class="rounded-lg bg-white p-6 shadow">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                Alumni Management
            </h2>

            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search by name, program, or employer..." aria-label="Search alumni" class="mt-4 w-full rounded-md border border-gray-300 px-3 py-2">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Program</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Graduation Date</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Current Employer</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Current Position</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                @forelse($alumni as $record)
                    <tr wire:key="alumni-{{ $record->id }}">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $record->user?->first_name }}
                            {{ $record->user?->last_name }}
                        </td>

                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $record->program }}
                        </td>

                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $record->graduation_date->format('Y-m-d') }}
                        </td>

                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $record->current_employer ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $record->current_position ?? '-' }}
                        </td>

                        <td class="space-x-2 px-4 py-3 text-sm">
                            <button type="button" wire:click="edit({{ $record->id }})" class="font-medium text-indigo-600 hover:text-indigo-800">
                                Edit
                            </button>

                            <button type="button" wire:click="confirmDelete({{ $record->id }})" class="font-medium text-red-600 hover:text-red-800">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            No alumni records found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $alumni->links() }}
        </div>
    </section>

    @if($editingAlumniId)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6" role="dialog" aria-modal="true" aria-labelledby="update-title">
            <div class="absolute inset-0 bg-gray-900/50" aria-hidden="true"></div>

            <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
                <h2 id="update-title" class="mb-6 text-xl font-semibold text-gray-900">
                    Update Alumni
                </h2>

                <form wire:submit="update" class="space-y-4">
                    <div>
                        <label for="program" class="mb-1 block text-sm font-medium text-gray-700">
                            Program
                        </label>

                        <input id="program" type="text" wire:model="program"
                            class="w-full rounded-md border border-gray-300 px-3 py-2">
                        @error('program')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="graduationDate" class="mb-1 block text-sm font-medium text-gray-700">
                            Graduation Date
                        </label>

                        <input id="graduationDate" type="date" wire:model="graduationDate"
                            class="w-full rounded-md border border-gray-300 px-3 py-2">
                        @error('graduationDate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="currentEmployer" class="mb-1 block text-sm font-medium text-gray-700">
                            Current Employer
                        </label>
                        <input id="currentEmployer" type="text" wire:model="currentEmployer"
                            class="w-full rounded-md border border-gray-300 px-3 py-2">
                        @error('currentEmployer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="currentPosition" class="mb-1 block text-sm font-medium text-gray-700">
                            Current Position
                        </label>
                        <input id="currentPosition" type="text" wire:model="currentPosition"
                            class="w-full rounded-md border border-gray-300 px-3 py-2">
                        @error('currentPosition')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="cancelEdit" class="rounded-md bg-gray-200 px-4 py-2 text-gray-800 hover:bg-gray-300">
                            Cancel
                        </button>

                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                            Update Alumni
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showSuccessModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4" role="dialog" aria-modal="true" aria-labelledby="success-title">
            <div class="absolute inset-0 bg-gray-900/50" aria-hidden="true"></div>

            <div class="relative w-full max-w-md rounded-lg bg-white p-6 text-center shadow-xl">
                <h2 id="success-title" class="text-xl font-semibold text-gray-900">
                    Alumni Updated
                </h2>

                <p class="mt-2 text-gray-600">
                    {{ $successMessage }}
                </p>

                <button type="button" wire:click="closeSuccessModal" class="mt-6 rounded-md bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700">
                    OK
                </button>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center px-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-title"
        >
            <div class="absolute inset-0 bg-gray-900/50" aria-hidden="true"></div>

            <div class="relative w-full max-w-md rounded-lg bg-white p-6 text-center shadow-xl">
                <h2 id="delete-title" class="text-xl font-semibold text-gray-900">
                    Delete Alumni
                </h2>

                <p class="mt-2 text-gray-600">
                    Are you sure you want to delete this alumni record?
                    This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-center gap-3">
                    <button type="button" wire:click="closeDeleteModal" class="rounded-md bg-gray-200 px-5 py-2 text-gray-800 hover:bg-gray-300">
                        Cancel
                    </button>

                    <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-5 py-2 text-white hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
