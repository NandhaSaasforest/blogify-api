<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\blogs;

class hashtags extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'usage_count',
    ];

    public function blogs()
    {
        return $this->belongsToMany(blogs::class, 'blog_hashtags', 'hashtag_id', 'blog_id'); // Use blog_hastags pivot table
    }
}
