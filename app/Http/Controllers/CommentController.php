<?php

namespace App\Http\Controllers;

use App\Models\blogs;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Get all comments for a blog
     */
    public function index(blogs $blog)
    {
        $comments = Comment::where('blog_id', $blog->id)
            ->topLevel()
            ->with(['author', 'replies.author'])
            ->latest()
            ->get()
            ->map(function ($comment) use ($blog) {
                return $this->formatComment($comment, $blog);
            });

        return response()->json([
            'data' => $comments,
            'total' => $comments->count(),
        ]);
    }

    /**
     * Store a new comment
     */
    public function store(Request $request, blogs $blog)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'blog_id' => $blog->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'parent_id' => null,
        ]);

        $comment->load('author');

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => $this->formatComment($comment, $blog),
        ], 201);
    }

    /**
     * Reply to a comment
     */
    public function reply(Request $request, blogs $blog, Comment $comment)
    {
        // Verify comment belongs to the blog
        if ($comment->blog_id !== $blog->id) {
            return response()->json([
                'message' => 'Comment not found for this blog'
            ], 404);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        // If replying to a reply, use the parent's parent_id
        $parentId = $comment->parent_id ?? $comment->id;

        $reply = Comment::create([
            'blog_id' => $blog->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'parent_id' => $parentId,
        ]);

        $reply->load('author');

        return response()->json([
            'message' => 'Reply added successfully',
            'data' => $this->formatComment($reply, $blog),
        ], 201);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, blogs $blog, Comment $comment)
    {
        // Verify ownership
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized to update this comment'
            ], 403);
        }

        // Verify comment belongs to the blog
        if ($comment->blog_id !== $blog->id) {
            return response()->json([
                'message' => 'Comment not found for this blog'
            ], 404);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($validated);
        $comment->load('author');

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => $this->formatComment($comment, $blog),
        ]);
    }

    /**
     * Delete a comment
     */
    public function destroy(blogs $blog, Comment $comment)
    {
        // Verify ownership or blog ownership
        if ($comment->user_id !== Auth::id() && $blog->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized to delete this comment'
            ], 403);
        }

        // Verify comment belongs to the blog
        if ($comment->blog_id !== $blog->id) {
            return response()->json([
                'message' => 'Comment not found for this blog'
            ], 404);
        }

        // Delete all replies first
        $comment->replies()->delete();
        
        // Delete the comment
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }

    /**
     * Like a comment
     */
    public function like(blogs $blog, Comment $comment)
    {
        // Implement your like logic here
        // This might involve a comment_likes pivot table
        
        return response()->json([
            'message' => 'Comment liked successfully'
        ]);
    }

    /**
     * Unlike a comment
     */
    public function unlike(blogs $blog, Comment $comment)
    {
        // Implement your unlike logic here
        
        return response()->json([
            'message' => 'Comment unliked successfully'
        ]);
    }

    /**
     * Format comment data for response
     */
    private function formatComment(Comment $comment, blogs $blog)
    {
        return [
            'id' => (string) $comment->id,
            'content' => $comment->content,
            'author' => [
                'id' => (string) $comment->author->id,
                'name' => $comment->author->name,
                'avatar' => $comment->author->avatar ?? $this->generateAvatar($comment->author->name),
            ],
            'createdAt' => $comment->created_at->diffForHumans(),
            'isAuthorReply' => $comment->user_id === $blog->user_id,
            'replies' => $comment->replies ? $comment->replies->map(function ($reply) use ($blog) {
                return $this->formatComment($reply, $blog);
            })->values() : [],
        ];
    }

    /**
     * Generate avatar emoji based on name
     */
    private function generateAvatar($name)
    {
        $avatars = ['👨', '👩', '🧑', '👴', '👵', '🧔', '👱', '👨‍💼', '👩‍💼'];
        $index = ord(strtoupper($name[0])) % count($avatars);
        return $avatars[$index];
    }
}
