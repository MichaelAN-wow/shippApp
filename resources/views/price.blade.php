<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On Hand Solution | Pricing</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon_io/favicon.ico') }}">
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon_io/android-chrome-192x192.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="512x512" href="{{ asset('images/favicon_io/android-chrome-512x512.png') }}">


    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/4.5.2/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/price.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
</head>

<body>
    @include('layouts.nav')

    <div class="container-fluid hero-container py-5">
        <div class="container">
            <div class="price">
                <img src="{{ asset('images/Text Cube.png') }}" alt="Price">
                <p class="why-us">Pricing</p>
            </div>
            
            <!-- Main Heading -->
            <h1 class="hero-heading">Pricing</h1>
            
          <h1 class="hero-heading text-center">Simple, Transparent Pricing for Makers</h1>
      <p class="lead text-center mb-5">All the tools you need to run your handmade business — no hidden fees, no tiers, just streamlined inventory and production.</p>

      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card-pricing text-center">
            <h2 class="highlight-purple">Everything Included</h2>
            <h3><strong>$39</strong> <span class="highlight-purple"> / month</span></h3>
            <p class="text-muted mb-2">or $421/year (save 10%)</p>
            <p class="text-muted mb-3">14-day free trial • Cancel anytime</p>
            <hr>

            <ul class="price-list text-left">
              <li><strong>Inventory & Materials</strong></li>
              <li>Materials Management <span><i class="fas fa-check"></i></span></li>
              <li>Category Management <span><i class="fas fa-check"></i></span></li>
              <li>Purchase Tracking <span><i class="fas fa-check"></i></span></li>

              <li class="mt-3"><strong>Production & Products</strong></li>
              <li>Product Management <span><i class="fas fa-check"></i></span></li>
              <li>Production Runs <span><i class="fas fa-check"></i></span></li>
              <li>Product Calculator <span><i class="fas fa-check"></i></span></li>
              <li>Smart Batch Tracking <span><i class="fas fa-check"></i></span></li>

              <li class="mt-3"><strong>Sales & Growth</strong></li>
              <li>Sales Tracking <span><i class="fas fa-check"></i></span></li>
              <li>Shopify Integration <span><i class="fas fa-check"></i></span></li>
              <li>Profit & Loss Tools <span><i class="fas fa-check"></i></span></li>

              <li class="mt-3"><strong>Team & Support</strong></li>
              <li>Team Management <span><i class="fas fa-check"></i></span></li>
              <li>User Permissions <span><i class="fas fa-check"></i></span></l>
               <li>Team Calendar <span><i class="fas fa-check"></i></span></li>
              <li>Dedicated Support <span><i class="fas fa-check"></i></span></li>
            </ul>

            <a href="/register" class="btn btn-warning btn-block trial mt-4"><strong>Start Free for 14 Days</strong></a>
          </div>
        </div>
      </div>
    </div>
  </div>
    
    <div class="container my-5">
        <h2 class="faq-header text-center">Pricing <span class="highlight-purple">FAQs</span></h2>

        <div id="accordion" class="accordion">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            How do I pay annually?
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </h5>
                </div>

                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        You can pay annually by selecting the annual payment option during checkout.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        If I decide to cancel my plan, will I receive a refund?
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                    <div class="card-body">
                        If you decide that On Hand Solution is not the right fit for you, you will continue to have access until the end of the current billing cycle.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingThree">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            I haven't launched my business yet, do I need an inventory system?
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </h5>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                    <div class="card-body">
                        Starting small enables steady growth, just as your brand will evolve and expand over time. Use our tools to effectively scale your business.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingFour">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            What happens if I cancel my plan?
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </h5>
                </div>
                <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                    <div class="card-body">
                    Even if you choose to cancel your plan, you will retain full access to our services until the end of your current billing cycle. We're confident you'll love what we provide!
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingFive">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            Are there plans for additional integrations?
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </h5>
                </div>
                <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
                    <div class="card-body">
                    Yes, we are constantly working on adding more integrations to improve functionality.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingSix">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            I have an existing system that I used previously, can I import the data?
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </h5>
                </div>
                <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
                    <div class="card-body">
                        Yes, we plan to expand to other platforms and introduce new features as we grow.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="headingSeven">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            Have a feature request?
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </h5>
                </div>
                <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                    <div class="card-body">
                        We’d love to hear from you! Please share your feature requests with us. You can contact us via email at <a href="mailto:hello@onhandsolution.com">hello@onhandsolution.com</a>.
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
