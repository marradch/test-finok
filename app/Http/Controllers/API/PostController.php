<?php
namespace App\Http\Controllers\API;

use App\Http\Resources\PostCollection;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PostResource;
use App\Jobs\ImportPosts;
use App\Services\WeatherService;

class PostController extends BaseController
{
    public function index(): JsonResponse
    {
        $posts = Post::paginate();

        return (new PostCollection($posts))->response();
    }

    public function show(Post $post): JsonResponse
    {
        $user = Auth::user();
        if ($user->can('view', $post)) {
            return (new PostResource($post))->response();
        } else {
            return $this->sendError('Not Authorized', Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->can('create', Post::class)) {
            $request->validate([
                'title' => 'bail|required|string|max:255',
                'content' => 'bail|required|string',
            ]);

            $post = Post::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'user_id' => $user->id,
            ]);

            return (new PostResource($post))->response()->setStatusCode(Response::HTTP_CREATED);
        } else {
            return $this->sendError('Not Authorized', Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $user = Auth::user();
        if ($user->can('update', $post)) {
            $request->validate([
                'title' => 'bail|required|string|max:255',
                'content' => 'bail|required|string',
            ]);

            $post->title = $request->input('title');
            $post->content = $request->input('content');
            $post->save();

            return (new PostResource($user))->response();
        } else {
            return $this->sendError('Not Authorized', Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(Post $post): JsonResponse
    {
        $user = Auth::user();

        if ($user->can('delete', $post)) {
            $post->delete();

            return $this->sendResponse([], 'Post deleted');
        } else {
            return $this->sendError('Not Authorized', Response::HTTP_UNAUTHORIZED);
        }
    }

    public function import(): JsonResponse
    {
        ImportPosts::dispatch();

        return $this->sendResponse([], 'Import of posts initiated');
    }

    public function weather(): JsonResponse
    {
        try {
            $data = (new WeatherService())->getWeatherInfo();

            return $this->sendResponse($data, 'Success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
