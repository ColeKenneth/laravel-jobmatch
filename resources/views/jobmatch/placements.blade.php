<x-jobmatch-layout portal title="Placement Records">
    <div class="workspace-heading">
        <span class="overline">YOUR WORKSPACE</span>
        <h1>Placement Records</h1>
        <p class="muted">Review recorded placements and filter by status.</p>
    </div>
    <section class="card">
        <h1>Placement Records</h1>
        <form class="placement-filter" method="GET">
            <label>
                Status
                <select name="status">
                    <option value="">All statuses</option>
                    @foreach(App\Enum\PlacementStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status')===$status->value)>{{ $status->getLabel() }}</option>
                    @endforeach
                </select>
            </label>
            <button class="button">Filter</button>
            <a href="{{ route('portal.placements') }}">Clear</a>
        </form>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Job</th>
                        <th>Employer</th>
                        <th>Student</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->jobPosting?->title }}</td>
                            <td>{{ $item->jobPosting?->employer?->company_name }}</td>
                            <td>{{ $item->student?->user?->full_name }}</td>
                            <td>
                                <span class="badge">{{ $item->placement_status->getLabel() }}</span>
                            </td>
                            <td>{{ $item->placement_date?->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No placement records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $items->links() }}</div>
    </section>
</x-jobmatch-layout>
