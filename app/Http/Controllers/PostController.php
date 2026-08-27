<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Services\PostService;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

// Keep this controller thin - validation lives in the FormRequests, the
// "can this user do that" check lives in PostPolicy, and the actual DB work
// lives in PostService. If a method here starts doing real logic that's a
// sign it should move to one of those instead.
class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Landing page. All the event data is loaded after the fact via
     * getEvents() (AJAX), this just returns the empty shell.
     */
    public function show()
    {
        return view('posts.show');
    }

    /**
     * Paginated event list for the landing page table. Hit from JS
     * (resources/js/public.js) on load and on every page click, no full
     * page reload.
     */
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
     * Full event + its packages, as JSON. Used by the "View Details" modal
     * AND by the "Edit" button to prefill the create-event form (see
     * openEditEventModal in public.js) - didnt seem worth 2 endpoints for
     * basically the same query.
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
     * Admin-only, enforced in StorePostRequest::authorize() (via PostPolicy)
     * not here - the FormRequest throws a 403 automatically before this
     * method even runs if the check fails.
     */
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
     * Same deal as store(), authorization happens inside UpdatePostRequest.
     * $post is resolved automatically from the {post} route param.
     */
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
     * No FormRequest for a plain delete (nothing to validate), so the
     * policy check has to happen manually here instead of in a request
     * class like the other two.
     */
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
