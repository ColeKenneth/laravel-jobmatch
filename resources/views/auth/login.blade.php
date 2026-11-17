<x-jobmatch-layout title="Login">
    <section class="campus-split">
        <div class="campus-photo" role="img" aria-label="World Citi Colleges campus"></div>
        <div class="campus-content auth-content">
            <section class="card auth-card">
                <span class="auth-school-icon">
                    <x-jobmatch-icon name="school"/>
                </span>
                <h1>WCC JobMatchAI · Multi-User Login</h1>
                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf
                    <label>
                        Email address
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Enter your email address">
                    </label>
                    <label>
                        Password
                        <span class="password-row">
                            <input type="password" name="password" required autocomplete="current-password" placeholder="Enter password">
                            <button type="button" data-password aria-label="Show password">Show</button>
                        </span>
                    </label>
                    <label class="check-label">
                        <input type="checkbox" name="remember">
                        Remember me
                    </label>
                    <button class="button stretch">Login</button>
                </form>
                <a href="{{ route('password.request') }}">Forgot Password?</a>
            </section>
        </div>
    </section>
</x-jobmatch-layout>
