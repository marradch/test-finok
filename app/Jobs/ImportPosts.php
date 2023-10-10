<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Posts;
use Illuminate\Support\Facades\Log;

class ImportPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::get("https://jsonplaceholder.typicode.com/posts");
        } catch(\Exception $e) {

            Log::error('posts import: http error');
        }

        if ($response->status() == 200) {
            $body = json_decode($response->body());

            if ($body) {
                foreach ($body as $outerPost) {
                    Post::create([
                        'title' => $outerPost->title,
                        'content' => $outerPost->body,
                    ]);
                }
            } else {

                Log::error('posts import: Third-party library error');
            }
        } else {

            Log::error('posts import: Third-party library error');
        }
    }
}
