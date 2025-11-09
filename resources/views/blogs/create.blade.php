@extends('layouts.admin_master')

@section('content')
<link href="{{ asset('plugins/froala_editor/froala_editor.pkgd.min.css') }}" rel="stylesheet">
<script src="{{ asset('plugins/froala_editor/froala_editor.pkgd.min.js') }}"></script>

<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Create Blog Post
    </div>
    <div class="card-body container">
        <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="mb-3">
                <label for="published_on" class="form-label">Published On</label>
                <input type="date" class="form-control" id="published_on" name="published_on" required>
            </div>

            <div class="mb-3">
                <label for="author_name" class="form-label">Author Name</label>
                <input type="text" class="form-control" id="author_name" name="author_name" required>
            </div>

            <div class="mb-3">
                <label for="author_job" class="form-label">Author Job</label>
                <input type="text" class="form-control" id="author_job" name="author_job" required>
            </div>

            <!-- File Input for Author Image with Preview -->
            <div class="mb-3">
                <label for="author_image" class="form-label">Author Image</label>
                <input type="file" class="form-control" id="author_image" name="author_image" accept="image/*" onchange="previewAuthorImage(event)">
                <img id="author_image_preview" src="#" alt="Author Image Preview" style="max-width: 200px; display:none; margin-top:10px;" />
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" required></textarea>
            </div>

            <!-- File Input for Content Image with Preview -->
            <div class="mb-3">
                <label for="content_image" class="form-label">Content Image</label>
                <input type="file" class="form-control" id="content_image" name="content_image" accept="image/*" onchange="previewContentImage(event)">
                <img id="content_image_preview" src="#" alt="Content Image Preview" style="max-width: 200px; display:none; margin-top:10px;" />
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="draft">Draft</option>
                    <option value="publish">Publish</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create Blog Post</button>
        </form>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Froala editor
        new FroalaEditor('#content', {
            key: "1C%kZV[IX)_SL}UJHAEFZMUJOYGYQE[\\ZJ]RAe(+%$==",
            attribution: false,
            heightMin: 300,
            toolbarButtons: ['bold', 'italic', 'underline', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'insertLink', 'insertImage'],
            placeholderText: 'Write your content here...'
        });
    });
    function previewAuthorImage(event) {
        const input = event.target;
        const preview = document.getElementById('author_image_preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // Show the image
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none'; // Hide the image if no file is selected
        }
    }

    function previewContentImage(event) {
        const input = event.target;
        const preview = document.getElementById('content_image_preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // Show the image
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none'; // Hide the image if no file is selected
        }
    }
</script>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif