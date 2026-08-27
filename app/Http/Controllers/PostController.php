<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Services\PostService;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function show()
    {
        return view('posts.show');
    }

    /**
     *
    **/
    public function getEvents(Request $request)
    {
        $events = Post::orderBy('event_date', 'asc')->paginate(4);

        return response()->json([
            'success' => true,
            'data' => $events->items(),
            'current_page' => $events->currentPage(),
            'last_page' => $events->lastPage(),
        ]);
    }

    /**
     * Packages for a single event, used by the "View Details" modal.
    **/
    public function getPackages($id)
    {
        $post = Post::with('packages')->findOrFail($id);

        return response()->json([
            'success' => true,
            'post' => $post->only(['id', 'title', 'subtitle', 'event_date', 'location', 'overview']),
            'packages' => $post->packages,
        ]);
    }

    /**
     *
    **/
    public function store(StorePostRequest $request)
    {
        try {
            $this->postService->createPostWithPackages($request->validated(), auth()->id());

            return redirect()->route('home')->with('success', 'The event has been successfully created.');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'An error occurred while creating the event.');
        }
    }

    /**
     *
    **/
    public function update(UpdatePostRequest $request, Post $post)
    {
        try {
            $this->postService->updatePostWithPackages($post, $request->validated());

            return redirect()->route('home')->with('success', 'The event has been successfully updated.');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'An error occurred while updating the event.');
        }
    }

    /**
     *
    **/
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        try {
            $this->postService->deletePost($post);

            return redirect()->route('home')->with('success', 'The event has been successfully deleted.');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'An error occurred while deleting the event.');
        }
    }
}
