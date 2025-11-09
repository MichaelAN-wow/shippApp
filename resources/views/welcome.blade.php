<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On Hand Solution</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon_io/favicon.ico') }}">
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon_io/android-chrome-192x192.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="512x512" href="{{ asset('images/favicon_io/android-chrome-512x512.png') }}">


    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/4.5.2/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
</head>

<body>
    @include('layouts.nav')

    <div class="container-fluid main-container py-5">
        <div class="container">
            <div class="row align-items-center main-container-row">
                <div class="col-md-6">
                    <div class="header-text mb-4">
                        <h1 class="light-text d-inline-block">Produce</h1>
                        <h1 class="light-text d-inline-block">Track</h1>
                    </div>
                    <div class="main-heading mb-4">
                        <h1 class="bold-text-Profit">Profit</h1>
                        <span class="icon ml-2">
                            <img src="{{ asset('icons/profitFullStopIcon.svg') }}" alt="Full Stop Icon">
                        </span>
                    </div>
                    <div class="description mb-4">
                        <p class="medium-text">ON HAND SOLUTION</p>
                        <p>From raw materials to final sale, we track it all.
Manage production runs, calculate COGS, log time, and sync everything with Shopify — without the spreadsheets.</p>
                    </div>
                    <div class="button-group">
                        <a href="/features" class="btn btn-outline-dark mr-2">Learn More</a>
                        <a href="/register" class="btn btn-warning">Get Started</a>
                    </div>
                </div>
                <div class="col-md-6 main-laptop">
                    <img src="{{ asset('images/mainImage.png') }}" alt="Main Image" class="img-fluid">
                </div>
            </div>
            <div class="main-container-blank"></div>
        </div>
    </div>

    <div class="container services-container text-center my-5 py-5">

        <h2 class="services-header">Take control of your<span class="highlight"> growing
                business</span>
        </h2>
        <p class="services-subtext mb-4">ON HAND SOLUTION helps empower your business and take it to new heights</p>
        <div class="row services-content">
            <div class="col-md-4 service-item">
                <img src="{{ asset('icons/settingsIcon.svg') }}" alt="COGS Calculation" class="mb-3">
                <h5 class="service-title">COGS Calculation</h5>
                <p class="service-description">Automatically calculate COGS.</p>
            </div>
            <div class="col-md-4 service-item">
                <img src="{{ asset('icons/cartIcon.svg') }}" alt="Shop Integration" class="mb-3">
                <h5 class="service-title">Shop Integration</h5>
                <p class="service-description">Sync with multiple platforms to ensure that product inventory never
                    runs
                    out.</p>
            </div>
            <div class="col-md-4 service-item">
                <img src="{{ asset('icons/cubeIcon.svg') }}" alt="Raw Material Tracking" class="mb-3">
                <h5 class="service-title">Raw Material Tracking</h5>
                <p class="service-description">Always know what raw materials you have on hand, as products are
                    made.
                </p>
            </div>
            <div class="row">
                <div class="col-md-4 service-item">
                    <img src="{{ asset('icons/cloudIcon.svg') }}" alt="Centralized Info" class="mb-3">
                    <h5 class="service-title">Centralized Info</h5>
                    <p class="service-description">View all of your sales and customers, from multiple channels, in one spot.</p>
                </div>
                <div class="col-md-4 service-item">
                    <img src="{{ asset('icons/heartFolderIcon.svg') }}" alt="Business Reports" class="mb-3">
                    <h5 class="service-title">Business Reports</h5>
                    <p class="service-description">Understand your business on a deeper level with inventory and sales reports.</p>
                </div>
                <div class="col-md-4 service-item">
                    <img src="{{ asset('icons/pieIcon.svg') }}" alt="Effective Solutions" class="mb-3">
                    <h5 class="service-title">Effective Solutions</h5>
                    <p class="service-description">Document supply orders, understand unit costs, perform audits, and more.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container platform-container py-5">
        <div class="row align-items-center">
            <div class="col-md-6 platform-text">
                <h2 class="platform-heading mb-4">
                    <span class="bold">Integrate with </span><span class="highlight-shopify bold">Shopify</span>
                </h2>
                <p class="platform-description mb-4">ON HAND SOLUTION offer syncing solutions for Shopify.</p>
                <p class="highlight-text">More platforms coming soon!</p>
                <a href="/features" class="btn btn-outline-dark">Learn More</a>
            </div>
            <div class="col-md-6 platform-image">
                <img src="{{ asset('images/integrate with shopify - graphic.png') }}" alt="Platform Image" class="img-fluid">
            </div>
        </div>
    </div>

    <!-- <div class="container insights-container text-center my-5 py-5">
        <h2 class="insights-heading mb-4">Let the <span class="highlight-text">Numbers</span> Speak for
            Themselves</h2>
        <p class="insights-subheading mb-4">Add team members and take control of the production flow like never
            before.
        </p>
        <div class="row insights-content">
            <div class="col-md-4 insight-item">
                <h3 class="insight-value highlight-text">1.67x</h3>
                <span class="insight-name">Sales</span>
                <span class="insight-desc">Another way to grow fast</span>
            </div>
            <div class="col-md-4 insight-item">
                <h3 class="insight-value highlight-text">29%</h3>
                <span class="insight-name">Customer Retention</span>
                <span class="insight-desc">On your website</span>
            </div>
            <div class="col-md-4 insight-item">
                <h3 class="insight-value highlight-text">19%</h3>
                <span class="insight-name">Extra Growth Revenue</span>
                <span class="insight-desc">From your sales</span>
            </div>
        </div>
        <div class="insight-learn-more-section">
            <hr class="line-above">
            <a href="#" class="btn btn-outline-dark mt-4 learn-more-btn">Learn More</a>
        </div>
    </div> -->

    <!-- <div class="container-fluid sponsors-container text-center my-5">
        <div class="row justify-content-center">
            <div class="col-4 col-md-2 d-flex align-items-center justify-content-center">
                <img src="{{ asset('icons/woocommerceLogo.svg') }}" alt="Woocommerce Logo" class="img-fluid">
            </div>
            <div class="col-4 col-md-2 d-flex align-items-center justify-content-center">
                <img src="{{ asset('icons/etsyIcon.svg') }}" alt="Etsy Logo" class="img-fluid">
            </div>
            <div class="col-4 col-md-2 d-flex align-items-center justify-content-center">
                <img src="{{ asset('icons/shopifyIcon.svg') }}" alt="Shopify Logo" class="img-fluid">
            </div>
            <div class="col-4 col-md-2 d-flex align-items-center justify-content-center">
                <img src="{{ asset('icons/squarespaceIcon.svg') }}" alt="Squarespace Logo" class="img-fluid">
            </div>
            <div class="col-4 col-md-2 d-flex align-items-center justify-content-center">
                <img src="{{ asset('icons/wixIcon.svg') }}" alt="Wix Logo" class="img-fluid">
            </div>
        </div>
    </div> -->


    <div class="container production-container text-center my-5">
        <p class="subtitle highlight-text">Seamless Production Management</p>
        <h2 class="title mb-5">Easily Manage <span class="highlight">Production</span></h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card p-2 mb-2 card-small">
                    <div class="d-flex">
                        <img src="{{ asset('icons/tickIcon.svg') }}" alt="Tick Icon" class="icon mr-2">
                        <p class="card-text mb-0"><strong>Planned:</strong> Queue up production runs by setting due
                            dates and
                            assigning products that need to be created.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-2 mb-2 card-small">
                    <div class="d-flex">
                        <img src="{{ asset('icons/tickIcon.svg') }}" alt="Tick Icon" class="icon mr-2">
                        <p class="card-text mb-0">
                        <p class="card-text"><strong>In-Progress:</strong> View a breakdown of all raw materials needed
                            to complete a production run.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-2 mb-2 card-small">
                    <div class="d-flex">
                        <img src="{{ asset('icons/tickIcon.svg') }}" alt="Tick Icon" class="icon mr-2">
                        <p class="card-text mb-0">
                        <p class="card-text"><strong>Choose location:</strong> Our platform allows you to manage and sync your inventory by specific locations. Effectively handle productions per selected location.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-2 mb-2 card-small">
                    <div class="d-flex">
                        <img src="{{ asset('icons/tickIcon.svg') }}" alt="Tick Icon" class="icon mr-2">
                        <p class="card-text mb-0">
                        <p class="card-text"><strong>Quick Reports:</strong> Our reporting system offers comprehensive insights into your business operations, enabling you to make data-driven decisions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container founder-container text-center my-5">
        <h2 class="title mb-5">Our <span class="highlight">Founders</span></h2>
        <p> Are we reinventing the wheel? Not at all. Instead, we’re simplifying the process and making it stress-free. We’ve carefully <br /> selected all the features we found useful and seamlessly integrated them into this platform. </p>
        <div class="row">
            <div class="col-md-6">
                <div class="founder-card">
                    <img src="{{ asset('images/founder.jpg') }}" alt="Doug Treadway">
                    <h3 class="founder-title">Doug Treadway</h3>
                    <p class="founder-role highlight-text bold">CEO</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="founder-card">
                    <img src="{{ asset('images/co-founder.jpg') }}" alt="Brant Treadway">
                    <h3 class="founder-title">Brant Treadway</h3>
                    <p class="founder-role highlight-text bold">Co-Founder</p>
                </div>
            </div>
            <a href="/who_we_are" class="btn btn-outline-dark btn-learn-more">Learn More</a>
        </div>
    </div>

    <div class="container grow-container text-center my-5">
        <h2 class="grow-header mb-4">Learn and <span class="highlight">grow</span> with us</h2>
        <p class="grow-subtext mb-5">The blocks & components you need</p>
        <div class="row" style="justify-content: center;">
            <div class="col-md-8 mb-4 text-center">
                <a href="{{ route('blogs.home') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                        <h5 class="card-title" style="font-weight: 600; font-size: 1.5rem; color: #333; margin-bottom: 1rem; line-height: 1.4;">
                            Empower Your Handmade Business with Expert Insights
                        </h5>
                        </div>
                        <img src="{{ asset('images/LEARN.png') }}" alt="Card Image 2" class="card-img-top">
                    </div>
                </a>
            </div>
        </div>
        </div>
    </div>

    @include('layouts.footer')

    <script src="{{ asset('libs/jquery/js/jquery-3.5.1.min.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('plugins/bootstrap/4.5.2/js/bootstrap.bundle.min.js') }}"></script>
</body>
