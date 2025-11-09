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
    <link rel="stylesheet" href="{{ asset('css/feature.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
</head>

<body>
    @include('layouts.nav')

    <div class="container-fluid hero-container py-5">
        <div class="container">
            <!-- Why Us? -->
            <div class="why-us">
                <img src="{{ asset('images/Text Cube.png') }}" alt="Why Us Icon">
                <p class="why-us">Why Us?</p>
            </div>
            
            <!-- Main Heading -->
            <h1 class="hero-heading">Streamline Your Inventory <br />Management</h1>
            
            <!-- Subheading -->
            <p class="subheading">ON HAND SOLUTION is a comprehensive inventory management system designed to help businesses efficiently track <br /> materials, products, and production processes.</p>
            
            <!-- Buttons -->
            <div>
                <a href="#" class="btn btn-outline-dark mr-2">Learn More</a>
                <a href="#" class="btn btn-warning">Get Started</a>
            </div>
            
            <!-- Image Section -->
            <img src="{{ asset('images/Hero section Mockup.png') }}" alt="Inventory Management Mockup">
        </div>
    </div>

    <div class="container-fluid hero-container material py-5">
        <div class="container">
            <h2 class="platform-heading mb-4">
            <span class="bold">Materials </span>
            <span class="highlight-purple bold">Management</span>
            </h2>
            <p class="platform-description mb-4">Organize and track your raw materials</p>
            <div class="full-content">
                <img src="{{ asset('images/materials_management.png') }}" alt="Materials Management Image" class="img-fluid hero-image">
                <p class="platform-description-detail mb-4"> Efficiently organize and track your raw materials. You can <span class="highlight-yellow">monitor current stock levels</span> and unit types, set minimum thresholds for automatic alerts, and record the Cost of Goods Sold (COGs) per unit. The system lets you sort materials by supplier for more organized purchase orders, categorize them for <span class="highlight-yellow">easier management</span>, and track the last order dates. Additionally, you can add images and notes to each material, ensuring that everything is <span class="highlight-yellow">easily accessible</span> and well-documented. </p>
            </div>
        </div>
    </div>

    <div class="container-fluid hero-container product py-5">
        <div class="container">
            <h2 class="platform-heading mb-4">
                <span class="highlight-purple bold">Product</span><span class="bold"> Management </span>
            </h2>
            <p class="platform-description mb-4">Track suppliers and orders</p>
            <div class="full-content">
                <p class="platform-description-detail mb-4">Efficiently manage your finished products and streamline production cost calculations with our comprehensive Product Management tool. Easily set product names, assign them to collections, and add <span class="highlight-yellow">stocking locations</span>. You can define <span class="highlight-yellow">minimum and current stock levels</span> for each location, and select <span class="highlight-yellow">raw materials</span> from a convenient dropdown menu. Adjust quantities and tracking units such as pieces, ounces, or grams, and let the system <span class="highlight-yellow">automatically calculate the Cost of Goods (COG)</span> based on the components used. <span class="highlight-yellow">Categorize your products</span>, add unique product codes, include notes, and upload images to keep everything organized and accessible.
                </p>
                <img src="{{ asset('images/product_management.png') }}" alt="Product Management Image" class="img-fluid hero-image">
            </div>
        </div>
    </div>

    <div class="container-fluid hero-container sales py-5">
        <div class="container">
            <h2 class="platform-heading mb-4">
                <span class="bold">Sales </span><span class="highlight-purple bold">Tracking</span>
            </h2>
            <p class="platform-description mb-4">Sync with Shopify to display your most recent sales data.</p>
            <div class="full-content">
                <img src="{{ asset('images/sales_tracking.png') }}" alt="Sales Tracking Image" class="img-fluid hero-image"/>
                <p class="platform-description-detail mb-4">Stay on top of your business with <span class="highlight-yellow">seamless sales tracking</span>. Our platform <span class="highlight-yellow">syncs with Shopify</span> to automatically display your most recent sales data, providing you with <span class="highlight-yellow">real-time insights</span> and helping you make informed decisions to drive your business forward.
                </p>
            </div>
        </div>
    </div>

    <div class="container-fluid hero-container py-5 production">
        <div class="container">
            <h2 class="platform-heading mb-4">
                <span class="highlight-purple bold">Production</span><span class="bold"> Management</span>
            </h2>
            <p class="platform-description mb-4">Streamline your production process</p>
            <div class="full-content">
                <p class="platform-description-detail mb-4">Streamline your production process with our comprehensive Production Management tool. Easily <span class="highlight-yellow">create new production</span> runs with custom names, assign specific locations, and record the team members involved. <span class="highlight-yellow">Set due dates</span> to keep everything on track, select items and quantities for production, and monitor the status from planned to in progress, all the way to completion. Once a production run is completed, your Shopify inventory will automatically update, ensuring seamless <span class="highlight-yellow">management and efficiency</span> across your operations.
                </p>
                <img src="{{ asset('images/production_management.png') }}" alt="Production Management Image" class="img-fluid hero-image">
            </div>
        </div>
    </div>

    <div class="container-fluid hero-container report py-5">
        <div class="container">
            <h2 class="platform-heading mb-4">
                <span class="bold">Reporting</span>
            </h2>
            <p class="platform-description mb-4">Generate Comprehensive Reports on.</p>
            <div class="full-content">
                <img src="{{ asset('images/reporting.png') }}" alt="Reporting Image" class="img-fluid hero-image"/>
                <p class="platform-description-detail mb-4">Our reporting system offers <span class="highlight-yellow">comprehensive insights into your business operations</span>, enabling you to make data-driven decisions. We provide detailed <span class="highlight-yellow">reports on sales performance</span>, including a comparison of total revenue versus the number of sales, helping you identify trends and <span class="highlight-yellow">optimize strategies</span>. You'll also get insights into your most popular products, allowing you to understand customer preferences better. Our reports extend to material inventory <span class="highlight-yellow">value versus purchases</span>, ensuring you maintain optimal stock levels without overspending. Additionally, we offer an <span class="highlight-yellow">in-depth inventory breakdown</span>, distinguishing between materials and finished products, so you can efficiently manage <span class="highlight-yellow">resources and production</span>. Explore these insights and more to stay ahead in your industry.
                </p>
            </div>
        </div>
    </div>

    <div class="container-fluid hero-container team py-5">
        <div class="container">
            <h2 class="platform-heading mb-4">
                <span class="highlight-purple bold">Team</span><span class="bold"> Management</span>
            </h2>
            <p class="platform-description mb-4">Track your team's work and payments</p>
            <div class="full-content">
                <p class="platform-description-detail mb-4">Team Management allows you to <span class="highlight-yellow">efficiently track your team's work</span> and payments by inputting team member names and hourly rates. You can <span class="highlight-yellow">monitor the time spent on tasks</span>, enabling <span class="highlight-yellow">clock-in and clock-out functionality</span> for accurate tracking. Additionally, you can manage <span class="highlight-yellow">user access</span> to the platform, ensuring that your team operates smoothly and securely.
                </p>
                <img src="{{ asset('images/team_management.png') }}" alt="Team Management Image" class="img-fluid hero-image">
            </div>
        </div>
    </div>

    <div class="container-fluid hero-container user py-5">
        <div class="container">
            <h2 class="platform-heading mb-4">
                <span class="highlight-purple bold">User</span><span class="bold"> Management</span>
            </h2>
            <p class="platform-description mb-4"Control access to your <strong>ON HAND SOLUTION</strong> platform</p>
            <div class="full-content">
                <img src="{{ asset('images/user_management.png') }}" alt="User Management Image" class="img-fluid hero-image">
                <p class="platform-description-detail mb-4">With the User Management feature on your <span class="highlight-yellow">ON HAND SOLUTION</span>platform, you can easily<span class="highlight-yellow">control and customize access</span>for your team. This feature allows you to add or remove users, assign specific roles such as employee or super admin, and tailor access to different <span class="highlight-yellow">features based on user roles</span>. This ensures that each team member has the appropriate level of access to perform their tasks efficiently and securely.
                </p>
            </div>
        </div>
    </div>

    <div class="container-fluid hero-container py-5 category">
        <div class="container">
            <h2 class="platform-heading mb-4">
                <span class="highlight-purple bold">Category</span><span class="bold"> Management</span>
            </h2>
            <p class="platform-description mb-4">Organize your inventory efficiently.</p>
            <div class="full-content">
                <p class="platform-description-detail mb-4">Efficiently <span class="highlight-yellow">organize your inventory</span> with our Category Management feature. Easily edit category names and merge collections as needed to <span class="highlight-yellow">maintain a streamlined and organized inventory</span>.
                </p>
                <img src="{{ asset('images/category_management.png') }}" alt="Category Management Image" class="img-fluid hero-image">
            </div>
        </div>
    </div>

    <div class="container-fluid hero-container shopify py-5">
        <div class="container">
            <h2 class="platform-heading mb-4">
                <span class="highlight-shopify bold">Shopify</span><span class="bold"> Integration</span>
            </h2>
            <p class="platform-description mb-4">Seamless connect with your Shopify store.</p>
            <div class="full-content">
                <img src="{{ asset('images/shopify_integration.png') }}" alt="Shopify Integration Image" class="img-fluid hero-image">
                <p class="platform-description-detail mb-4">Effortlessly <span class="highlight-yellow">integrate with your Shopify</span>store to streamline your operations. Our solution allows you to link ON HAND SOLUTION products directly to your Shopify products, ensuring<span class="highlight-yellow">seamless synchronization</span> of inventory levels. With automatic updates, you can <span class="highlight-yellow">keep your stock levels in check</span> without lifting a finger. For added control, you can <span class="highlight-yellow">manually initiate sync cycles</span> whenever necessary, providing you with<span class="highlight-yellow">flexibility and accuracy</span> in managing your store's inventory.
                </p>
            </div>
        </div>
    </div>

    
    @include('layouts.footer')

    <script src="{{ asset('libs/jquery/js/jquery-3.5.1.min.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('plugins/bootstrap/4.5.2/js/bootstrap.bundle.min.js') }}"></script>

    <script>
         document.addEventListener('scroll', function() {
            const materialContainer = document.querySelector('.material');
            const containerHeight = materialContainer.getBoundingClientRect().height;

            if (window.scrollY > materialContainer.offsetTop - 100 &&window.scrollY < materialContainer.offsetTop + 500) {
                materialContainer.classList.add('scrolled');
            } else {
                materialContainer.classList.remove('scrolled');
            }

            const salesContainer = document.querySelector('.sales');

            if (window.scrollY > salesContainer.offsetTop - 100 &&window.scrollY < salesContainer.offsetTop + 500) {
                salesContainer.classList.add('scrolled');
            } else {
                salesContainer.classList.remove('scrolled');
            }

            const reportContainer = document.querySelector('.report');

            if (window.scrollY > reportContainer.offsetTop - 100 &&window.scrollY < reportContainer.offsetTop + 300) {
                reportContainer.classList.add('scrolled');
            } else {
                reportContainer.classList.remove('scrolled');
            }

            const userContainer = document.querySelector('.user');

            if (window.scrollY > userContainer.offsetTop - 100 &&window.scrollY < userContainer.offsetTop + 300) {
                userContainer.classList.add('scrolled');
            } else {
                userContainer.classList.remove('scrolled');
            }

            const shopifyContainer = document.querySelector('.shopify');

            if (window.scrollY > shopifyContainer.offsetTop - 100 &&window.scrollY < shopifyContainer.offsetTop + 300) {
                shopifyContainer.classList.add('scrolled');
            } else {
                shopifyContainer.classList.remove('scrolled');
            }

            const categoryContainer = document.querySelector('.category');

            if (window.scrollY > categoryContainer.offsetTop - 100 &&window.scrollY < categoryContainer.offsetTop + 300) {
                categoryContainer.classList.add('scrolled');
            } else {
                categoryContainer.classList.remove('scrolled');
            }

            const teamContainer = document.querySelector('.team');

            if (window.scrollY > teamContainer.offsetTop - 100 &&window.scrollY < teamContainer.offsetTop + 300) {
                teamContainer.classList.add('scrolled');
            } else {
                teamContainer.classList.remove('scrolled');
            }

            const productionContainer = document.querySelector('.production');

            if (window.scrollY > productionContainer.offsetTop - 100 &&window.scrollY < productionContainer.offsetTop + 300) {
                productionContainer.classList.add('scrolled');
            } else {
                productionContainer.classList.remove('scrolled');
            }

            const productContainer = document.querySelector('.product');

            if (window.scrollY > productContainer.offsetTop - 100 &&window.scrollY < productContainer.offsetTop + 300) {
                productContainer.classList.add('scrolled');
            } else {
                productContainer.classList.remove('scrolled');
            }
            
        });
    </script>
</body>

</html>
