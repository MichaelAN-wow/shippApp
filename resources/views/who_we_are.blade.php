<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On Hand Solution | Who We Are</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon_io/favicon.ico') }}">
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon_io/android-chrome-192x192.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="512x512" href="{{ asset('images/favicon_io/android-chrome-512x512.png') }}">


    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/4.5.2/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/who_we_are.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
</head>

<body>
  @include('layouts.nav')

  <!-- HERO SECTION -->
  <div class="container-fluid header-container py-5">
    <div class="container">
      <div class="row">
        <div class="col-xl-8 col-md-8 header-left">
          <div class="who_we_are_header">
            <img src="{{ asset('images/Text Cube.png') }}" alt="Who We Are">
            <p class="why-us">Who We Are</p>
          </div>
          <h1 class="page-title">ON HAND SOLUTION</h1>
          <h2 class="subtitle text-muted" style="font-size: 20px;">Built by makers, for makers.</h2>
          <p class="hero-subtext" style="font-size: 16px; color: #777;">
            Because we lived the mess — overordering, underpricing, and not knowing what was in stock. We couldn’t find the platform we needed, so we built it.
          </p>
        </div>
        <div class="col-xl-4 col-md-4 header-right">
          <img class="container-img" src="{{ asset('images/who_we_are_graphics.png') }}" alt="Dashboard Visual">
        </div>
      </div>
    </div>
  </div>

  <!-- PLATFORM OVERVIEW -->
  <div class="container-fluid sub-container py-5">
    <div class="container">
      <div class="row">
        <div class="col-xl-4 col-md-4">
          <img class="container-img" src="{{ asset('images/who_we_are_platform.png') }}" alt="Platform Preview">
        </div>
        <div class="col-xl-8 col-md-8 sub-container-right">
          <h5 class="container-title">Made to Make Sense</h5>
          <div class="container-content">
            On Hand Solution is more than inventory tracking — it’s peace of mind for small business owners. You can manage raw materials, track time, prep for markets, calculate COGS, and see how your business is really performing — all in one place. Built to grow with you, not overwhelm you.
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FEATURE VALUE PROP -->
  <div class="container-fluid sub-container py-5">
    <div class="container">
      <div class="row">
        <div class="col-xl-6 col-md-6 sub-container-left">
          <h5 class="container-title">Why We Built It</h5>
          <div class="container-content">
            We tried spreadsheets, sticky notes, and expensive platforms that didn’t understand how handmade businesses actually work. So we built On Hand Solution to solve the problems we lived through:
            <br><br>
            ✅ Overspending on materials<br>
            ✅ Forgetting what’s in stock<br>
            ✅ Losing track of production & pricing<br>
            ✅ Guessing instead of knowing your numbers
          </div>
        </div>
        <div class="col-xl-6 col-md-6 sub-container-right">
          <img class="container-img" src="{{ asset('images/who_we_are_key_features.png') }}" alt="Key Features">
        </div>
      </div>
    </div>
  </div>

  <!-- FOUNDER’S JOURNEY -->
  <div class="container-fluid sub-container founders-container py-5">
    <h2 class="text-center mb-4" style="font-weight: bold; font-size: 28px;">
      Our Journey → Why This Platform Exists
    </h2>
    <div class="row">
      <div class="col-xl-6 col-md-6 founders-image">
        <img src="{{ asset('images/founders.jpg') }}" alt="Founders">
      </div>
      <div class="col-xl-6 col-md-6 sub-container-right">
        <div class="founder-sub-container">
          <h5 class="container-sub-title">Starting Small</h5>
          <div class="container-content">
            In 2020, Doug and Brant started a home-based lifestyle brand to manage stress. It quickly became something more — the beginning of a handmade business built with love.
          </div>
        </div>
        <div class="founder-sub-container">
          <h5 class="container-sub-title">From Kitchen to Business</h5>
          <div class="container-content">
            Our brand grew — and so did the chaos. We created labels, designed packaging, filled orders by hand, and ran out of space (and energy). We knew we needed a better system.
          </div>
        </div>
        <div class="founder-sub-container">
          <h5 class="container-sub-title">Scaling Up</h5>
          <div class="container-content">
            Spreadsheets turned into scattered digital notes. What started with a few SKUs became dozens. We were growing — but tracking everything was becoming a full-time job.
          </div>
        </div>
        <div class="founder-sub-container">
          <h5 class="container-sub-title">Finding a Fix</h5>
          <div class="container-content">
            Every platform we tried was either too corporate or too clunky, or required multiple platforms to do it all. None of them got what it meant to run a maker business. So we built the one we wish we had all along.
          </div>
        </div>
        <div class="founder-sub-container">
          <h5 class="container-sub-title">A Platform for All of Us</h5>
          <div class="container-content">
            On Hand Solution is now used by other handmade brands who need better tools. We built it for ourselves — and we’re sharing it with you.
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CLOSING CTA -->
  <div class="container-fluid sub-container py-5">
    <div class="container">
      <div class="row">
        <div class="col-xl-6 col-md-6 sub-container-left">
          <h5 class="container-title">What We Believe</h5>
          <div class="container-content">
            We’re not reinventing the wheel — we’re simplifying it. On Hand Solution is built to be clean, fast, and easy to use. No clutter. No overthinking. Just clarity.
            <br><br>
            Whether you’re a solo maker or scaling a small team, we want to help you make smart decisions and free up brain space for what you actually love: your craft.
          </div>
        </div>
        <div class="col-xl-6 col-md-6 sub-container-right">
          <h5 class="container-title">Join Us</h5>
          <div class="container-content">
            We believe small business should feel powerful, not overwhelming. If you’re ready to organize, streamline, and actually *see* your numbers — let’s get to work.
          </div>
          <a href="/register" class="btn btn-warning btn-lg mt-3"><strong>Get Started Today</strong></a>
        </div>
      </div>
    </div>
  </div>
       </div>
            </div>
        </div>
    </div>
</div>
    @include('layouts.footer')

    <script src="{{ asset('libs/jquery/js/jquery-3.5.1.min.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('plugins/bootstrap/4.5.2/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/font-awesome/5.15.1/js/all.min.js') }}" crossorigin="anonymous"></script>
</body>

</html>
