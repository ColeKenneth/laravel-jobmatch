<x-jobmatch-layout title="Register">
    <section class="campus-split">
        <div class="campus-photo" role="img" aria-label="World Citi Colleges campus"></div>
        <div class="campus-content auth-content">
            <h1>Join WCC JobMatchAI · Create Your Account</h1>
            @if(!in_array(request('type'),['student','alumni','employer'],true))
                <section class="card registration-choice">
                    <h2>Select your account type to begin:</h2>
                    <div class="choice-grid">
                        <div>
                            <span aria-hidden="true">🎓</span>
                            <h2>Student / Alumni</h2>
                            <p>Register as a WCC student or graduate.</p>
                            <a class="button" href="{{ route('register',['type'=>'student']) }}">Create Student Account</a>
                            <a class="text-button" href="{{ route('register',['type'=>'alumni']) }}">Register as Alumni</a>
                        </div>
                        <div>
                            <span aria-hidden="true">▦</span>
                            <h2>Employer</h2>
                            <p>Register as a recruiter / company.</p>
                            <a class="button" href="{{ route('register',['type'=>'employer']) }}">Create Employer Account</a>
                        </div>
                    </div>
                </section>
            @else
                <section class="card auth-card registration-form">
                    <a href="{{ route('register') }}">← Choose account type</a>
                    <h2>{{ ucfirst(request('type')) }} Registration</h2>
                    <form class="form-grid" method="POST" action="{{ route('register',['type'=>request('type')]) }}">
                        @csrf
                        <input type="hidden" name="role" value="{{ request('type') }}">
                        <label>
                            First name
                            <input name="first_name" value="{{ old('first_name') }}" required maxlength="100" autocomplete="given-name">
                        </label>
                        <label>
                            Last name
                            <input name="last_name" value="{{ old('last_name') }}" required maxlength="100" autocomplete="family-name">
                        </label>
                        <label>
                            Middle name (optional)
                            <input name="middle_name" value="{{ old('middle_name') }}" maxlength="100" autocomplete="additional-name">
                        </label>
                        <label>
                            Email
                            <input name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                        </label>
                        <label>
                            Password
                            <input type="password" name="password" required autocomplete="new-password">
                        </label>
                        <label>
                            Confirm password
                            <input type="password" name="password_confirmation" required autocomplete="new-password">
                        </label>
                        @if(Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <label class="check-label full">
                                <input type="checkbox" name="terms" value="1" required>
                                I agree to the
                                <a href="{{ route('terms.show') }}">Terms of Service</a>
                                and
                                <a href="{{ route('policy.show') }}">Privacy Policy</a>
                                .
                            </label>
                        @endif
                        <div class="form-actions">
                            <button class="button stretch">Create Account</button>
                        </div>
                    </form>
                </section>
            @endif
            <p>
                Already have an account?
                <a href="{{ route('login') }}">Login</a>
            </p>
        </div>
    </section>
</x-jobmatch-layout>
