<x-jobmatch-layout portal title="Company Profile">
    <div class="workspace-heading">
        <span class="overline">YOUR WORKSPACE</span>
        <h1>Company Profile</h1>
        <p class="muted">Manage the company information shown in the employer directory.</p>
    </div>
    <section class="card form-card">
        <h1>Company Profile</h1>
        <form class="form-grid" method="POST" action="{{ route('portal.company.update') }}">
            @csrf
            @method('PUT')
            <label>
                Company name
                <input name="company_name" type="text" value="{{ old('company_name', $company?->company_name) }}" required >
            </label>
            <label>
                Industry
                <input list="company-industries" name="industry" type="text" value="{{ old('industry', $company?->industry) }}" required >
            </label>
            <datalist id="company-industries">
                @foreach(App\Enum\EmployerIndustry::cases() as $industry)
                    <option value="{{ $industry->value }}"></option>
                @endforeach
            </datalist>
            <label>
                Location
                <input name="location" value="{{ old('location',$company?->location) }}" maxlength="100">
            </label>
            <label>
                Company size
                <select name="company_size">
                    <option value="">Not specified</option>
                    @foreach(App\Enum\CompanySize::cases() as $size)
                        <option value="{{ $size->value }}" @selected(old('company_size',$company?->company_size)===$size->value)>{{ $size->getLabel() }} employees</option>
                    @endforeach
                </select>
            </label>
            <label>
                Website
                <input name="website" type="url" value="{{ old('website', $company?->website) }}"  >
            </label>
            <label>
                Logo URL
                <input name="logo_url" type="url" value="{{ old('logo_url', $company?->logo_url) }}" required >
            </label>
            <label class="full">Company description<textarea name="company_description" rows="6">{{ old('company_description',$company?->company_description) }}</textarea></label>
            <div class="form-actions">
                <button class="button">Save Company</button>
            </div>
        </form>
    </section>
</x-jobmatch-layout>
