<div class="p-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Alumni Directory</h1>
        <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded">Add Alumni</a>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search by name, program, or employer..."
            class="w-full border rounded p-2"
        >
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 border-b">
            <tr>
                <th class="p-3">Name</th>
                <th class="p-3">Program</th>
                <th class="p-3">Graduation Year</th>
                <th class="p-3">Current Employer</th>
                <th class="p-3">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($alumniList as $alumni)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $alumni->user->full_name }}</td>
                    <td class="p-3">{{ $alumni->program }}</td>
                    <td class="p-3">{{ $alumni->graduation_year }}</td>
                    <td class="p-3">{{ $alumni->current_employer ?? 'N/A' }}</td>
                    <td class="p-3 flex gap-2">
                        <button class="text-blue-600 hover:underline">Edit</button>
                        <button
                            wire:click="delete({{ $alumni->id }})"
                            wire:confirm="Are you sure you want to delete this record?"
                            class="text-red-600 hover:underline"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-3 text-center text-gray-500">No alumni found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
