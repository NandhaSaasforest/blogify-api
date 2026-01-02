<?php

namespace App\Policies;

use App\Models\blogs;
use App\Models\User;

class BlogPolicy
{
    public function update(User $user, blogs $blog): bool
    {
        return $user->id === $blog->user_id;
    }

    public function delete(User $user, blogs $blog): bool
    {
        return $user->id === $blog->user_id;
    }
}