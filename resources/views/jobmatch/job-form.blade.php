<label>
    Job title
    <input name="title" type="text" value="{{ old('title', $job?->title) }}" required >
</label>
<label>
    Location
    <input name="location" type="text" value="{{ old('location', $job?->location) }}" required >
</label>
<label>
    Minimum experience (years)
    <input name="min_experience_years" type="number" value="{{ old('min_experience_years', $job?->min_experience_years) }}" required >
</label>
<label>
    Minimum salary
    <input name="min_salary" type="number" value="{{ old('min_salary', $job?->min_salary) }}" required >
</label>
<label>
    Maximum salary
    <input name="max_salary" type="number" value="{{ old('max_salary', $job?->max_salary) }}" required >
</label>
<label>
    Employment type
    <select name="employment_type">
        @foreach(App\Enum\EmploymentType::cases() as $type)
            <option value="{{ $type->value }}" @selected(old('employment_type',$job?->employment_type?->value)===$type->value)>{{ $type->getLabel() }}</option>
        @endforeach
    </select>
</label>
<label>
    Deadline
    <input type="date" name="deadline" value="{{ old('deadline',$job?->deadline?->format('Y-m-d')) }}" required>
</label>
<label>
    Status
    <select name="is_active">
        <option value="1" @selected(old('is_active',$job?->is_active ?? true))>Active</option>
        <option value="0" @selected(!old('is_active',$job?->is_active ?? true))>Closed</option>
    </select>
</label>
<label class="full">Job description<textarea name="description" rows="4" required>{{ old('description',$job?->description) }}</textarea></label>
<label class="full">Qualifications<textarea name="requirements" rows="4" required>{{ old('requirements',$job?->requirements) }}</textarea></label>
<label class="full">Preferred skills<textarea name="preferred_skills" rows="4" >{{ old('preferred_skills',$job?->preferred_skills) }}</textarea></label>
<div class="form-actions">
    <button class="button">Save Job</button>
</div>
