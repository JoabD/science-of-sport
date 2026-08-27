<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PostService
{
    /**
     * Creates a new Post with its related packages using a database transaction.
     *
     * @param array $data Validated request data
     * @return Post
     * @throws Exception
     */
    public function createPostWithPackages(array $data, int $userId): Post // Hmmm, this is a good name? I guess :D
    {
        try {
            return DB::transaction(function () use ($data, $userId) {
                // Main Event
                $post = Post::create([
                    'user_id' => $userId,
                    'title' => $data['title'],
                    'subtitle' => $data['subtitle'] ?? null,
                    'event_date' => $data['event_date'],
                    'location' => $data['location'],
                    'overview' => $data['overview'],
                ]);

                // Create associated packages if they exist
                if (isset($data['packages']) && is_array($data['packages'])) {
                    $post->packages()->createMany($data['packages']);
                }

                return $post;
            });
        } catch (Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Updates an existing Post and, if a new set of packages was submitted,
     * replaces the old ones with it.
     *
     * @param array $data Validated request data
     * @return Post
     * @throws Exception
     */
    public function updatePostWithPackages(Post $post, array $data): Post
    {
        try {
            return DB::transaction(function () use ($post, $data) {
                $post->update([
                    'title' => $data['title'],
                    'subtitle' => $data['subtitle'] ?? null,
                    'event_date' => $data['event_date'],
                    'location' => $data['location'],
                    'overview' => $data['overview'],
                ]);

                if (isset($data['packages']) && is_array($data['packages'])) {
                    $post->packages()->delete();
                    $post->packages()->createMany($data['packages']);
                }

                return $post;
            });
        } catch (Exception $e) {
            Log::error('Error updating post: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Deletes a Post. Its packages cascade-delete at the DB level.
     *
     * @throws Exception
     */
    public function deletePost(Post $post): void
    {
        try {
            $post->delete();
        } catch (Exception $e) {
            Log::error('Error deleting post: ' . $e->getMessage());
            throw $e;
        }
    }
}
