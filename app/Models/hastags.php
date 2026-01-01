<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hastags extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'usage_count',
    ];

    public function blogs()
    {
        return $this->belongsToMany(blogs::class);
    }
}
