<x-app-layout title="My Profile">

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="mb-4">
        <h4 class="mb-0">My Profile</h4>
    </div>

    {{-- PROFILE CARD --}}
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center">
            <x-user-avatar :user="$user" :size="80" class="me-3 flex-shrink-0" />

            <div class="flex-grow-1">
                <h4 class="mb-0 text-dark">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" id="profilePhotoForm">
                        @csrf
                        <label class="btn btn-sm btn-outline-primary mb-0">
                            <i class="mdi mdi-camera me-1"></i> {{ $user->profile_photo ? 'Change Photo' : 'Upload Photo' }}
                            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="d-none"
                                   onchange="this.form.submit()">
                        </label>
                    </form>
                    @if ($user->profile_photo)
                        <form method="POST" action="{{ route('profile.photo.remove') }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="mdi mdi-delete me-1"></i> Remove</button>
                        </form>
                    @endif
                    <small class="text-muted">JPG, PNG or WEBP, up to 5 MB. Without a photo your initials are shown.</small>
                </div>
                @error('photo')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">

        {{-- PERSONAL INFO --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Personal Information</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" name="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="form-control">
                        </div>

                        <button class="btn btn-primary w-100">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- CHANGE PASSWORD --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Change Password</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Old Password</label>
                            <input type="password" name="current_password"
                                   class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password"
                                   class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control">
                        </div>

                        <button class="btn btn-primary w-100">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

</x-app-layout>
