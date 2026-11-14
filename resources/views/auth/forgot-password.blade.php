<x-jobmatch-layout title="Password Recovery">
    <section class="campus-split">
        <div class="campus-photo" role="img" aria-label="World Citi Colleges campus"></div>
        <div class="campus-content auth-content">
            <section class="card auth-card">
                <span class="auth-school-icon">
                    <x-jobmatch-icon name="school"/>
                </span>
                <h1>WCC JobMatchAI · Password Recovery</h1>
                <p>Enter your registered email to receive a password reset link.</p>
                <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <label>
                        Email address
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                    </label>
                    <button class="button stretch">Send Reset Link</button>
                </form>
                <a href="{{ route('login') }}">Back to Login</a>
            </section>
        </div>
    </section>
</x-jobmatch-layout>
