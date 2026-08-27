<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Laravel calls before() first for every ability on this policy. If it
     * returns true/false that decides the whole check and none of the
     * methods below even run. Returning null means "no opinion, go check
     * the specific method instead" - which is what happens for non admins,
     * so create/update/delete just say no.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->role?->name === 'admin' ? true : null;
    }

    // Used by the "+ Create New Event" button (@can in the blade) and by
    // StorePostRequest::authorize()
    public function create(User $user): bool
    {
        return false;
    }

    // Used by UpdatePostRequest::authorize() for the Edit action
    public function update(User $user, Post $post): bool
    {
        return false;
    }

    // Used by PostController::destroy() directly, no FormRequest for a delete
    public function delete(User $user, Post $post): bool
    {
        return false;
    }
}
