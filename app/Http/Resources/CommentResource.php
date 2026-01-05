<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (string) $this->id,
            'content' => $this->content,
            'author' => [
                'id' => (string) $this->author->id,
                'name' => $this->author->name,
                'avatar' => $this->author->avatar ?? '👤',
            ],
            'createdAt' => $this->created_at->diffForHumans(),
            'updatedAt' => $this->updated_at->diffForHumans(),
            'isAuthorReply' => $this->isAuthorReply(),
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
