<nav class="navbar navbar-expand-lg navbar-light py-3">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('images/nav_bar_logo.png') }}" alt="NavBar Logo" height="59" width="106">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="/" id="home-link">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('features') ? 'active' : '' }}" href="/features">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('price') ? 'active' : '' }}" href="/price">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('who_we_are') ? 'active' : '' }}" href="/who_we_are">Who We Are</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('blogs*') ? 'active' : '' }}" href="/blogs/home">Blogs</a>
                </li>
                <!-- Login Link - Always Visible -->
                <li class="nav-item">
                    <a href="/login" class="nav-link d-flex align-items-center {{ Request::is('login') ? 'active' : '' }}">
                        <img src="{{ asset('icons/loginUserIcon.svg') }}" alt="Login Icon" class="mr-2" height="19" width="19"> Login
                    </a>
                </li>
                <!-- Register Button - Responsive -->
                <li class="nav-item d-lg-none">
                    <a href="/register" class="nav-link {{ Request::is('register') ? 'active' : '' }}">Get Started</a>
                </li>
            </ul>
            <!-- Desktop Register Button -->
            <div class="navbar-nav d-none d-lg-block ml-2">
                <a href="/register" class="btn btn-warning {{ Request::is('register') ? 'active' : '' }}">Get Started</a>
            </div>
        </div>
    </div>
</nav>
