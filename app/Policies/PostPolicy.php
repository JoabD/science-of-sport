<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Only admins get to manage events. This runs before every ability
     * below, so "is this user an admin" lives in exactly one place.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->role?->name === 'admin' ? true : null;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Post $post): bool
    {
        return false;
    }

    public function delete(User $user, Post $post): bool
    {
        return false;
    }
}
