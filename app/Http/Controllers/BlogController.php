<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Blog;

class BlogController extends Controller
{

    public function home()
{
    
    $blogs = Blog::where('status', 'publish')
                 ->orderBy('published_on', 'desc')
                 ->get();

    return view('blogs.home', compact('blogs'));
}

    public function index()
    {
        $blogs = Blog::all();
        return view('blogs.index', compact('blogs'));
    }

    // Show the form for creating a new blog post
    public function create()
    {
        return view('blogs.create');
    }

    // Store a newly created blog post in the database
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'published_on' => 'required|date',
            'author_name' => 'required|string|max:255',
            'author_job' => 'required|string|max:255',
            'author_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'content' => 'required',
            'content_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'status' => 'required|in:draft,publish',
        ]);

        // Handle the Author Image Upload
        if ($request->hasFile('author_image')) {
            $authorImagePath = $request->file('author_image')->store('blogs', 'public');
            $data['author_image_url'] = $authorImagePath;
        }

        // Handle the Content Image Upload
        if ($request->hasFile('content_image')) {
            $contentImagePath = $request->file('content_image')->store('blogs', 'public');
            $data['content_image_url'] = $contentImagePath;
        }

        // Create the blog post with the validated data
        Blog::create($data);

        // Redirect back to the blog index with success message
        return redirect()->route('blogs.index')->with('success', 'Blog created successfully');
    }

    // Show the form for editing the specified blog post
    public function edit(Blog $blog)
    {
        return view('blogs.edit', compact('blog'));
    }

    // Update the specified blog post in the database
    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'published_on' => 'required|date',
            'author_name' => 'required|string',
            'author_job' => 'required|string',
            'author_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required',
            'content_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:draft,publish',
        ]);

        // Handle the Author Image Upload
        if ($request->hasFile('author_image')) {
            $authorImagePath = $request->file('author_image')->store('blogs', 'public');
            $data['author_image_url'] = $authorImagePath;
        }

        // Handle the Content Image Upload
        if ($request->hasFile('content_image')) {
            $contentImagePath = $request->file('content_image')->store('blogs', 'public');
            $data['content_image_url'] = $contentImagePath;
        }

        // Update the blog post
        $blog->update($data);

        return redirect()->route('blogs.index')->with('success', 'Blog updated successfully');
    }

    // Remove the specified blog post from the database
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('blogs.index')->with('success', 'Blog deleted successfully');
    }

    public function show($id)
    {
        // Fetch the current blog by ID
        $blog = Blog::findOrFail($id);

        // Fetch related posts (for simplicity, fetch other blog posts)
        // You can modify the logic to get related posts based on category or tags
        $relatedBlogs = Blog::where('id', '!=', $id)
                            ->orderBy('published_on', 'desc')
                            ->take(4) // Limit to 4 related posts
                            ->get();

        // Return the view with the blog and related blogs
        return view('blogs.show', compact('blog', 'relatedBlogs'));
    }
}
