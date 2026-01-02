<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'followers_count',
        'following_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function blogs()
    {
        return $this->hasMany(blogs::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(bookmarks::class);
    }

    public function bookmarkedBlogs()
    {
        return $this->belongsToMany(blogs::class, 'bookmarks', 'user_id', 'blog_id');
    }

    // Users I follow
    public function following()
    {
        return $this->belongsToMany(
            User::class,
            'user_follows',
            'follower_id',
            'following_id'
        )->withTimestamps();
    }

    // Users who follow me
    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'user_follows',
            'following_id',
            'follower_id'
        )->withTimestamps();
    }

    // Helper
    public function isFollowing($userId): bool
    {
        return $this->following()
            ->where('following_id', $userId)
            ->exists();
    }
}
