<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - ON HAND SOLUTION</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon_io/favicon.ico') }}">
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="192x192"
        href="{{ asset('images/favicon_io/android-chrome-192x192.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="512x512"
        href="{{ asset('images/favicon_io/android-chrome-512x512.png') }}">

    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/4.5.2/css/bootstrap.min.css') }}">
    <link href="/frontend/plugins/toast/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-container">
        <div class="banner-container">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/nav_bar_logo.png') }}" alt="ON HAND SOLUTION Logo">
            </a>
            <h6>Log in and level up your <span style="color: #ffd700;">growing business</span>!</h6>
        </div>
        <form method="POST" action="{{ route('login') }}" class="register-form" id="login-form">
            @csrf
            <div class="form-group input-group">
                <input type="email" name="email" id="email" placeholder="Email" class="form-control" required />
            </div>
            <div class="form-group input-group">
                <input type="password" name="password" id="password" placeholder="Password" class="form-control"
                    autocomplete="new-password" required />
            </div>
            <div class="forgot-password">
                <a href="#">Forgot Password</a>
            </div>
            <div class="form-group form-button">
                <input type="submit" name="signin" id="signin" class="form-submit btn btn-yellow btn-block"
                    value="Log in" />
            </div>
            <div class="or-separator">
                <span>OR LOG IN WITH</span>
            </div>
            <div class="social-buttons">
                <button type="button" class="btn btn-google">
                    <img src="{{ asset('frontend/images/google.svg') }}" alt="Google Icon"> Google
                </button>
                <button type="button" class="btn btn-apple">
                    <img src="{{ asset('frontend/images/apple.svg') }}" alt="Apple Icon"> Apple
                </button>
                <button type="button" class="btn btn-facebook">
                    <img src="{{ asset('frontend/images/facebook.svg') }}" alt="Facebook Icon"> Facebook
                </button>
            </div>
        </form>
        <div class="mt-4">
            <a href="{{ route('register') }}" class="create-account-link">Create a new account</a>
        </div>
    </div>

    <script src="{{ asset('frontend/vendor/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/jquery-ui/1.12.1/popper.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/4.5.2/js/bootstrap.bundle.min.js') }}"></script>

    <script src="/frontend/plugins/toast/toastr.min.js"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>

    <!-- Toastr Notifications -->
    <script>
    @if(session('error'))
    toastr.error('{{ session('error') }}');
    @endif

    @if(session('success'))
    toastr.success('{{ session('success') }}');
    @endif
    </script>
</body>
</html>