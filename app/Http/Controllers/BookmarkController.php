<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\bookmarks;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $blogs = $request->user()
            ->bookmarkedBlogs()
            ->with(['user', 'hashtags'])
            ->latest('bookmarks.created_at')
            ->paginate(10);

        return BlogResource::collection($blogs);
    }

    public function store(Request $request, Blog $blog)
    {
        $bookmark = bookmarks::firstOrCreate([
            'user_id' => $request->user()->id,
            'blog_id' => $blog->id,
        ]);

        return response()->json([
            'message' => 'Blog bookmarked successfully',
            'isBookmarked' => true,
        ]);
    }

    public function destroy(Request $request, Blog $blog)
    {
        bookmarks::where('user_id', $request->user()->id)
            ->where('blog_id', $blog->id)
            ->delete();

        return response()->json([
            'message' => 'Bookmark removed successfully',
            'isBookmarked' => false,
        ]);
    }
}