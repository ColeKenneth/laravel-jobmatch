<x-jobmatch-layout portal title="My Certifications">
    <div class="workspace-heading">
        <span class="overline">YOUR WORKSPACE</span>
        <h1>My Certifications</h1>
        <p class="muted">Keep your certification details together in your career profile.</p>
    </div>
    <section class="card">
        <div class="section-heading">
            <h1>Certifications</h1>
            <a href="{{ route('portal.skills') }}">Manage Skills →</a>
        </div>
        @if(!$student)
            <p>Complete your student profile first.</p>
            <a class="button" href="{{ route('portal.profile') }}">Complete Profile</a>
        @else
            <form class="form-grid" method="POST" action="{{ route('portal.certifications.save') }}">
                @csrf
                <label>
                    Certificate name
                    <input name="name" type="text" value="{{ old('name') }}" required >
                </label>
                <label>
                    Issuing authority
                    <input name="issuing_authority" type="text" value="{{ old('issuing_authority') }}" required >
                </label>
                <label>
                    Date issued
                    <input name="date_issued" type="date" value="{{ old('date_issued') }}" required >
                </label>
                <label>
                    Expiry date (optional)
                    <input name="expiry_date" type="date" value="{{ old('expiry_date') }}"  >
                </label>
                <label>
                    Verification URL (optional)
                    <input name="verification_url" type="url" value="{{ old('verification_url') }}"  >
                </label>
                <label>
                    Credential ID (optional)
                    <input name="credential_id" type="text" value="{{ old('credential_id') }}"  >
                </label>
                <div class="form-actions">
                    <button class="button">Save Certification</button>
                </div>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Certificate</th>
                            <th>Issuer</th>
                            <th>Issued</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->issuing_authority }}</td>
                                <td>{{ $item->date_issued?->format('M j, Y') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('portal.certifications.delete',$item->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-button danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No certifications added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-jobmatch-layout>
