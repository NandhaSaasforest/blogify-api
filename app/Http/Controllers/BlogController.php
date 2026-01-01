<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\Hashtag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with(['user', 'hashtags']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('hashtags', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by hashtag
        if ($request->has('hashtag')) {
            $query->whereHas('hashtags', function($q) use ($request) {
                $q->where('name', $request->hashtag);
            });
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $blogs = $query->latest()->paginate(10);

        return BlogResource::collection($blogs);
    }

    public function store(BlogRequest $request)
    {
        $blog = Blog::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // Handle hashtags
        if ($request->has('hashtags') && is_array($request->hashtags)) {
            foreach ($request->hashtags as $tagName) {
                $tagName = trim($tagName);
                if (empty($tagName)) continue;
                
                // Remove # if present
                $tagName = ltrim($tagName, '#');
                
                $hashtag = Hashtag::firstOrCreate(
                    ['name' => "#{$tagName}"],
                    ['usage_count' => 0]
                );
                
                $hashtag->increment('usage_count');
                $blog->hashtags()->attach($hashtag->id);
            }
        }

        return new BlogResource($blog->load(['user', 'hashtags']));
    }

    public function show(Blog $blog)
    {
        return new BlogResource($blog->load(['user', 'hashtags']));
    }

    public function update(BlogRequest $request, Blog $blog)
    {
        $this->authorize('update', $blog);

        $blog->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // Update hashtags
        if ($request->has('hashtags')) {
            // Remove old hashtag relationships
            foreach ($blog->hashtags as $hashtag) {
                $hashtag->decrement('usage_count');
            }
            $blog->hashtags()->detach();

            // Add new hashtags
            foreach ($request->hashtags as $tagName) {
                $tagName = trim($tagName);
                if (empty($tagName)) continue;
                
                $tagName = ltrim($tagName, '#');
                
                $hashtag = Hashtag::firstOrCreate(
                    ['name' => "#{$tagName}"],
                    ['usage_count' => 0]
                );
                
                $hashtag->increment('usage_count');
                $blog->hashtags()->attach($hashtag->id);
            }
        }

        return new BlogResource($blog->load(['user', 'hashtags']));
    }

    public function destroy(Blog $blog)
    {
        $this->authorize('delete', $blog);

        // Decrement hashtag usage
        foreach ($blog->hashtags as $hashtag) {
            $hashtag->decrement('usage_count');
        }

        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted successfully',
        ]);
    }

    public function like(Request $request, Blog $blog)
    {
        $blog->increment('likes');

        return response()->json([
            'message' => 'Blog liked',
            'likes' => $blog->likes,
        ]);
    }

    public function unlike(Request $request, Blog $blog)
    {
        if ($blog->likes > 0) {
            $blog->decrement('likes');
        }

        return response()->json([
            'message' => 'Blog unliked',
            'likes' => $blog->likes,
        ]);
    }
}