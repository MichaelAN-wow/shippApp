<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register | On Hand Solution</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon_io/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">

    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/4.5.2/css/bootstrap.min.css') }}">
    <link href="/frontend/plugins/toast/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<body>
    <div class="register-container">
        <!-- Left Side: Form -->
        <div class="form-section">
            <div class="register-banner-container text-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/nav_bar_logo.png') }}" alt="ON HAND SOLUTION Logo">
                </a>
                <h6>Sign up and level up your <br><span style="color: #ffd700;">growing business</span>!</h6>
            </div>

            <form method="POST" action="{{ route('register') }}" class="register-form" id="register-form">
                @csrf
                <div class="form-group input-group">
                    <input type="text" name="company_name" id="company_name" placeholder="Your Company Name" class="form-control"/>
                </div>
                <div class="form-group input-group">
                    <input type="text" name="name" id="name" placeholder="Your Name" class="form-control" required />
                </div>
                <div class="form-group input-group">
                    <input type="email" name="email" id="email" placeholder="Email" class="form-control" required autofocus />
                </div>
                <div class="form-group input-group">
                    <input type="password" name="password" id="password" placeholder="Password" class="form-control" autocomplete="off" required />
                </div>
                <div class="form-group input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm your password" class="form-control" autocomplete="off" required />
                </div>

                <div class="form-group input-group">
                    <div class="cf-turnstile" data-sitekey="{{ env('CLOUDFLARE_SITE_KEY') }}"></div>
                </div>

                <div class="form-group form-button">
                    <input type="submit" class="form-submit btn btn-yellow btn-block" value="Sign Up" />
                </div>

                <div class="or-separator">
                    <span>OR SIGN UP WITH</span>
                </div>

                <div class="social-buttons text-center">
                    <button type="button" class="btn btn-google mb-2">
                        <img src="{{ asset('frontend/images/google.svg') }}" alt="Google Icon"> Google
                    </button>
                    <button type="button" class="btn btn-apple mb-2">
                        <img src="{{ asset('frontend/images/apple.svg') }}" alt="Apple Icon"> Apple
                    </button>
                    <button type="button" class="btn btn-facebook mb-2">
                        <img src="{{ asset('frontend/images/facebook.svg') }}" alt="Facebook Icon"> Facebook
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="create-account-link">Log into an existing account</a>
                <p class="text-muted mt-2" style="font-size: 13px;">
                    Need help? <a href="mailto:hello@onhandsolution.com">hello@onhandsolution.com</a> or text 402‑378‑9083
                </p>
            </div>
        </div>

         <div class="info-section">
            <div class="signup-promo">
                <h2>Sign up for <span>free!</span></h2>
                <p>Track raw materials, do production runs, track time, figure out cost of goods, sync inventory with
                    Shopify. <br/><strong>Track, produce, profit.</strong></p>
            </div>
            <div class="quote-box">
                <blockquote>
                    "ON HAND SOLUTION allows you to track all of your raw materials for hand made items, do production runs that sync your inventory, figure out cost of goods while tracking your time with your team, and keep track of your material purchases.<br/> It’s like your small business’s new best friend."
                </blockquote>
                <p class="quote-author">From Doug Treadway - CEO</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('frontend/vendor/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="{{ asset('plugins/bootstrap/4.5.2/js/bootstrap.bundle.min.js') }}"></script>
    <script src="/frontend/plugins/toast/toastr.min.js"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>

    <!-- Form Validation & Toasts -->
    <script>
        function validateForm(event) {
            event.preventDefault();
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password_confirmation').value;

            if (password.length < 5) {
                toastr.error('Password must be at least 5 characters long.');
                return false;
            }

            if (password !== confirmPassword) {
                toastr.error('Passwords do not match.');
                return false;
            }

            document.getElementById('register-form').submit();
        }

        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif

        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif
    </script>
</body>
</html>
