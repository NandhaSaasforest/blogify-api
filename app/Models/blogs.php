<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class blogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'likes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hashtags()
    {
        return $this->belongsToMany(hashtags::class, 'blog_hashtags', 'blog_id', 'hashtag_id'); // Use blog_hastags pivot table
    }

    public function bookmarks()
    {
        return $this->hasMany(bookmarks::class, 'blog_id');
    }

    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'blog_id', 'user_id');
    }

    public function isBookmarkedBy($userId)
    {
        return $this->bookmarkedBy()
            ->where('user_id', $userId)
            ->exists();
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'post_likes', 'blog_id', 'user_id');
    }
}
