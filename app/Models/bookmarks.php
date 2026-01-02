<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\blogs;

class bookmarks extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blog_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blog()
    {
        return $this->belongsTo(blogs::class);
    }
}
