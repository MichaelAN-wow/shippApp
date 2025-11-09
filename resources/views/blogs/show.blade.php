<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $blog->title }}</title>

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

    <div class="container-fluid">
        <div class="row">
            <!-- Blog Content -->
            <div class="col-md-8 py-5 blog-panel">
                <div class="blog-content">
                    <p class="text-muted">{{ $blog->published_on->format('jS F Y') }} - 5 min read</p>
                    <h1>{{ $blog->title }}</h1>
                    {!! $blog->content !!}
                </div>
                <div class="author-section mt-5 p-4">
                    <h5 class="author-heading">Author</h5>
                    <div class="d-flex align-items-center author-info">
                        <div class="author-image mr-3">
                            @if ($blog->author_image_url)
                                <img src="{{ asset('storage/' . $blog->author_image_url) }}" alt="{{ $blog->author_name }}" class="rounded-circle" width="80" height="80">
                            @else
                                <img src="{{ asset('images/default_avater.png') }}" alt="{{ $blog->author_name }}" class="rounded-circle" width="80" height="80">
                            @endif
                        </div>
                        <div class="author-details">
                            <p><strong>Written By:</strong> {{ $blog->author_name }}</p>
                            <p class="text-muted">{{ $blog->author_job }}</p>
                            <p><strong>Published On:</strong> {{ $blog->published_on->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Posts -->
            <div class="col-md-4 py-5 ">
                <h5>Related Posts</h5>
                <div class="related-posts container">
                    @foreach($relatedBlogs as $relatedBlog)
                    <div class="card mb-4">
                        @if($relatedBlog->content_image_url)
                        <img src="{{ asset('storage/' . $relatedBlog->content_image_url) }}" class="card-img-top" alt="{{ $relatedBlog->title }}">
                        @else
                        <img src="{{ asset('images/blog_default.png') }}" class="card-img-top" alt="Placeholder Image">
                        @endif
                        <div class="card-body">
                            <p class="card-text"><small class="text-muted">{{ $relatedBlog->published_on->format('F j, Y') }} - 5 min read</small></p>
                            <h6 class="card-title">{{ $relatedBlog->title }}</h6>
                            <a href="{{ route('blogs.show', $relatedBlog->id) }}" class="btn-read-more">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
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
