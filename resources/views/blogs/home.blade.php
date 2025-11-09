<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On Hand Solution | Blogs</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon_io/favicon.ico') }}">
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon_io/android-chrome-192x192.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="512x512" href="{{ asset('images/favicon_io/android-chrome-512x512.png') }}">


    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/4.5.2/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home_blogs.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
</head>

<body>
    @include('layouts.nav')

    <div class="container-fluid header-container py-5">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-md-8 header-left">
                    <div class="home_blogs">
                        <img src="{{ asset('images/Text Cube.png') }}" alt="Explore, Engage, Elevate">
                        <p class="why-us">Explore, Engage, Elevate</p>
                    </div>
                    <h4>Blogs</h4>
                    <h5>Real talk, maker hacks, and inventory insights that actually matter.</h5>
                </div>
                <div class="col-xl-4 col-md-4" class="header-right">
                    <img class="container-img" src="{{ asset('images/who_we_are_graphics.png') }}" alt="Graphic">
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid sub-container py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="search" placeholder="Search for Blogs...">
                </div>
                <!-- <div class="col-md-4">
                    <select id="categoryFilter" class="form-control">
                        <option value="all">All Categories</option>
                        <option value=".category1">Category 1</option>
                        <option value=".category2">Category 2</option>
                        <option value=".category3">Category 3</option>
                    </select>
                </div> -->
                <div class="col-md-4">
                    <select id="sortFilter" class="form-control">
                        <option value="default">Sort By</option>
                        <option value="date:asc">Date (Ascending)</option>
                        <option value="date:desc">Date (Descending)</option>
                    </select>
                </div>
            </div>
            <!-- Blog Cards -->
            <div class="row" id="mixContainer">
                @foreach($blogs as $blog)
                <div class="col-md-4 mix" data-date="{{ $blog->published_on->format('Ymd') }}">
                    <div class="card mb-4">
                        @if($blog->content_image_url)
                        <img src="{{ asset('storage/' . $blog->content_image_url) }}" class="card-img-top" alt="{{ $blog->title }}">
                        @else
                        <img src="{{ asset('images/blog_default.png') }}" class="card-img-top" alt="Placeholder Image">
                        @endif
                        <div class="card-body" style="min-height: 200px">
                            <p class="card-text"><small class="text-muted">{{ $blog->published_on->format('F j, Y') }}</small></p>
                            <h5 class="card-title">{{ $blog->title }}</h5>
                            <a href="{{ route('blogs.show', $blog->id) }}" class="btn-read-more">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @include('layouts.footer')

    <script src="{{ asset('libs/jquery/js/jquery-3.5.1.min.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('plugins/bootstrap/4.5.2/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/font-awesome/5.15.1/js/all.min.js') }}" crossorigin="anonymous"></script>

    <script src="{{ asset('plugins/mixitup/mixitup.min.js') }}"></script>
    <script src="{{ asset('plugins/mixitup/mixitup-pagination.min.js') }}"></script>
    
<script>
    // Initialize MixItUp for sorting and filtering
    var containerEl = document.querySelector('#mixContainer');
    var mixer = mixitup(containerEl, {
        selectors: {
            target: '.mix'
        },
        animation: {
            duration: 300
        },
        pagination: {
            limit: 100,
            generatePageList: true,
        }
    });

    // Category Filter
    // document.querySelector('#categoryFilter').addEventListener('change', function() {
    //     var filterValue = this.value;
    //     mixer.filter(filterValue);
    // });

    // Sorting based on date
    document.querySelector('#sortFilter').addEventListener('change', function() {
        var sortValue = this.value;
        if (sortValue === 'date:asc') {
            mixer.sort('date:asc');
        } else if (sortValue === 'date:desc') {
            mixer.sort('date:desc');
        } else {
            mixer.sort('default');
        }
    });

    // Search Filter
    document.querySelector('#search').addEventListener('keyup', function() {
        var searchText = this.value.toLowerCase();

        var blogItems = document.querySelectorAll('#mixContainer .mix'); // Select all blog cards

        blogItems.forEach(function(item) {
            var title = item.querySelector('.card-title').textContent.toLowerCase();

            // If searchText is empty, show all items
            if (searchText === '') {
                item.style.display = 'block'; // Show all items
            } else if (title.includes(searchText)) {
                item.style.display = 'block'; // Show item if it matches
            } else {
                item.style.display = 'none'; // Hide item if it doesn't match
            }
        });

        // Update the MixItUp state (so pagination or other actions are in sync)
        mixer.filter(function() {
            return true; // Allow all elements that are manually visible to be included
        });
    });
</script>
</body>

</html>
