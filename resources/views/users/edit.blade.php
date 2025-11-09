@extends('layouts.admin_master')

@section('content')
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Edit User
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('users.updateAccount', $user->id) }}" id="userForm">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required readonly>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="off">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="off">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                    <option value="unauthorized" {{ old('type', $user->type) == 'unauthorized' ? 'selected' : '' }}>Unauthorized</option>
                    <option value="employee" {{ old('type', $user->type) == 'employee' ? 'selected' : '' }}>Employee</option>
                    <option value="admin" {{ old('type', $user->type) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('type', $user->type) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection

@section('script')
    <link href="/frontend/plugins/toast/toastr.min.css" rel="stylesheet" />
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('userForm');
            form.addEventListener('submit', function(event) {
                const passwordInput = form.querySelector('input[name="password"]');
                const confirmPasswordInput = form.querySelector('input[name="password_confirmation"]');
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                let valid = true;

                // Check if password is provided
                if (password) {
                    // Check if password length is less than 4 characters
                    if (password.length < 4) {
                        passwordInput.classList.add('is-invalid');
                        valid = false;
                    } else {
                        passwordInput.classList.remove('is-invalid');
                    }

                    // Check if passwords match
                    if (password !== confirmPassword) {
                        confirmPasswordInput.classList.add('is-invalid');
                        valid = false;
                    } else {
                        confirmPasswordInput.classList.remove('is-invalid');
                    }
                } else {
                    passwordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.remove('is-invalid');
                }

                if (!valid) {
                    event.preventDefault(); // Prevent form submission if validation fails
                }
            });

            // Display success or error messages
            @if(session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if(session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@endsection
