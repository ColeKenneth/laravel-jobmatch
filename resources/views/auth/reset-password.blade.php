<x-jobmatch-layout title="Reset Password">
    <section class="campus-split">
        <div class="campus-photo" role="img" aria-label="World Citi Colleges campus"></div>
        <div class="campus-content auth-content">
            <section class="card auth-card">
                <h1>Reset Your Password</h1>
                <form class="auth-form" method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <label>
                        Email
                        <input name="email" type="email" value="{{ old('email',$request->email) }}" required autocomplete="email">
                    </label>
                    <label>
                        New password
                        <input name="password" type="password" required autocomplete="new-password">
                    </label>
                    <label>
                        Confirm password
                        <input name="password_confirmation" type="password" required autocomplete="new-password">
                    </label>
                    <button class="button stretch">Reset Password</button>
                </form>
            </section>
        </div>
    </section>
</x-jobmatch-layout>
