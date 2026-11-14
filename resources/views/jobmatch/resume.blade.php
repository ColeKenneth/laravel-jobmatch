<x-jobmatch-layout portal title="My Resume">
    <div class="workspace-heading">
        <span class="overline">YOUR CAREER PROFILE</span>
        <h1>My Resume</h1>
        <p class="muted">Keep a current PDF resume available in your account.</p>
    </div>
    <div class="resume-grid">
        <section class="card form-card">
            @if(!$student)
                <h2>Start with your profile</h2>
                <p>Complete your student profile before uploading a resume.</p>
                <a class="button" href="{{ route('portal.profile') }}">Complete Profile</a>
            @else
                @if($student->resume_file_path)
                    <div class="resume-current">
                        <span class="section-icon">
                            <x-jobmatch-icon name="certificate"/>
                        </span>
                        <div>
                            <h2>Current resume</h2>
                            <p class="muted">A resume is available in your account.</p>
                        </div>
                        <a class="button outline" href="{{ route('portal.resume.download') }}">Download Current Resume</a>
                    </div>
                @else
                    <div class="resume-current">
                        <span class="section-icon">
                            <x-jobmatch-icon name="certificate"/>
                        </span>
                        <div>
                            <h2>No resume uploaded yet</h2>
                            <p class="muted">Choose a PDF below to get started.</p>
                        </div>
                    </div>
                @endif
                <form class="form-grid" action="{{ route('portal.resume.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="full">
                        Resume PDF (maximum 5 MB)
                        <input name="resume" type="file" accept="application/pdf,.pdf" required aria-describedby="resume-help">
                        <small id="resume-help">PDF format only. Uploading replaces your current resume.</small>
                    </label>
                    <div class="form-actions">
                        <button class="button">Upload Resume →</button>
                    </div>
                </form>
            @endif
        </section>
        <aside class="card resume-guide">
            <span class="section-icon">
                <x-jobmatch-icon name="briefcase"/>
            </span>
            <h2>Make your experience clear</h2>
            <ol>
                <li>Keep your contact information current.</li>
                <li>Highlight relevant education, skills, and experience.</li>
                <li>Use a readable layout and check spelling.</li>
                <li>Save as a PDF under 5 MB before uploading.</li>
            </ol>
            <a href="{{ route('portal.profile') }}">Review My Profile →</a>
        </aside>
    </div>
</x-jobmatch-layout>
