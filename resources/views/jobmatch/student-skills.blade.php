<x-jobmatch-layout portal title="My Skills">
    <div class="workspace-heading">
        <span class="overline">YOUR WORKSPACE</span>
        <h1>My Skills</h1>
        <p class="muted">Record your skills, proficiency, and experience.</p>
    </div>
    <section class="card">
        <div class="section-heading">
            <h1>Skills Inventory &amp; Proficiency</h1>
            <a href="{{ route('portal.certifications') }}">Manage Certifications →</a>
        </div>
        @if(!$student)
            <p>Complete your student profile first.</p>
            <a class="button" href="{{ route('portal.profile') }}">Complete Profile</a>
        @else
            <form class="form-grid" method="POST" action="{{ route('portal.skills.save') }}">
                @csrf
                <label>
                    Skill
                    <select name="skill_id" required>
                        <option value="">Choose a skill</option>
                        @foreach($skills as $skill)
                            <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Proficiency level
                    <select name="proficiency_level">
                        @foreach([1=>'Beginner',2=>'Basic',3=>'Intermediate',4=>'Advanced',5=>'Expert'] as $v=>$label)
                            <option value="{{ $v }}">{{ $v }} — {{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Years of experience
                    <input type="number" name="years_of_experience" min="0" max="60" value="0">
                </label>
                <div class="form-actions">
                    <button class="button">Save Skill</button>
                </div>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Skill</th>
                            <th>Proficiency</th>
                            <th>Experience</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->skill?->name }}</td>
                                <td>{{ $item->proficiency_level }}/5</td>
                                <td>{{ $item->years_of_experience }} years</td>
                                <td>
                                    <form method="POST" action="{{ route('portal.skills.delete',$item->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-button danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No skills added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-jobmatch-layout>
