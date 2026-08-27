{{-- Login / Register modals. Only rendered for guests, see layouts/public.blade.php --}}

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white bg-brand-blue">
                <h5 class="modal-title fw-bold" id="loginModalLabel">Log In</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="form" value="login">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="login_email" class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="login_email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="login_password" class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="login_password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="login_remember">
                        <label class="form-check-label" for="login_remember">Remember me</label>
                    </div>
                </div>
                <div class="modal-footer">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="me-auto small text-muted">Forgot your password?</a>
                    @endif
                    <button type="submit" class="btn btn-brand fw-bold">Log In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white bg-brand-blue">
                <h5 class="modal-title fw-bold" id="registerModalLabel">Create an Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="form" value="register">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="register_name" class="form-label fw-bold">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="register_name" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="register_email" class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="register_email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="register_password" class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="register_password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="register_password_confirmation" class="form-label fw-bold">Confirm Password</label>
                        <input type="password" class="form-control" id="register_password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-brand fw-bold">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>
