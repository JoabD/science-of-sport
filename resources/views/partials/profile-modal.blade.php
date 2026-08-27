<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white bg-brand-blue">
                <h5 class="modal-title fw-bold" id="profileModalLabel">My Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-info-tab" data-bs-toggle="tab" data-bs-target="#profile-info-pane" type="button" role="tab">
                            Profile
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-password-tab" data-bs-toggle="tab" data-bs-target="#profile-password-pane" type="button" role="tab">
                            Password
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-danger" id="profile-delete-tab" data-bs-toggle="tab" data-bs-target="#profile-delete-pane" type="button" role="tab">
                            Delete Account
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="profile-info-pane" role="tabpanel">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="form" value="profile-info">

                            <div class="mb-3">
                                <label for="profile_name" class="form-label fw-bold">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="profile_name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="profile_email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="profile_email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="form-text">
                                        Your email address is unverified.
                                        <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">Resend verification email</button>
                                    </div>

                                    @if (session('status') === 'verification-link-sent')
                                        <p class="text-success small mt-1 mb-0">A new verification link has been sent to your email address.</p>
                                    @endif
                                @endif
                            </div>

                            @if (session('status') === 'profile-updated')
                                <p class="text-success small mb-3">Saved.</p>
                            @endif

                            <button type="submit" class="btn btn-brand fw-bold">Save Changes</button>
                        </form>

                        <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="d-none">
                            @csrf
                        </form>
                    </div>

                    <div class="tab-pane fade" id="profile-password-pane" role="tabpanel">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form" value="profile-password">

                            <div class="mb-3">
                                <label for="profile_current_password" class="form-label fw-bold">Current Password</label>
                                <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="profile_current_password" name="current_password">
                                @error('current_password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="profile_new_password" class="form-label fw-bold">New Password</label>
                                <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="profile_new_password" name="password">
                                @error('password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="profile_new_password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                                <input type="password" class="form-control" id="profile_new_password_confirmation" name="password_confirmation">
                            </div>

                            @if (session('status') === 'password-updated')
                                <p class="text-success small mb-3">Saved.</p>
                            @endif

                            <button type="submit" class="btn btn-brand fw-bold">Update Password</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="profile-delete-pane" role="tabpanel">
                        <p class="text-muted">Once your account is deleted, all of its resources and data will be permanently deleted. This cannot be undone.</p>

                        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="form" value="profile-delete">

                            <div class="mb-3">
                                <label for="profile_delete_password" class="form-label fw-bold">Password</label>
                                <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" id="profile_delete_password" name="password" placeholder="Confirm your password">
                                @error('password', 'userDeletion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-danger fw-bold">Delete My Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
