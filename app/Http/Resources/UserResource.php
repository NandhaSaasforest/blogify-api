<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'bio' => $this->bio,
            'joinDate' => $this->created_at->format('F Y'),
            'postsCount' => $this->blogs()->count(),
            'followersCount' => $this->follower_count,
            'followingCount' => $this->following_count
        ];
    }
}