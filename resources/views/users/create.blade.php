@extends('layouts.admin_master')

@section('content')
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Add User
    </div>
    <div class="card-body">
        <form id="userForm" method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" required>
                <div class="invalid-feedback">Password must be at least 8 characters long.</div>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
                <div class="invalid-feedback">Passwords do not match.</div>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" class="form-control" required>
                    <option value="employee">Employee</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('userForm');
            form.addEventListener('submit', function(event) {
                const passwordInput = form.querySelector('input[name="password"]');
                const confirmPasswordInput = form.querySelector('input[name="password_confirmation"]');
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                let valid = true;

                // Check if password length is less than 8 characters
                if (password.length < 8) {
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

                if (!valid) {
                    event.preventDefault(); // Prevent form submission if validation fails
                }
            });
        });
    </script>
@endsection
