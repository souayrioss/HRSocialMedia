<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'visibility' => 'required|in:public,friends_only',
        ]);
        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'visibility' => $request->visibility,
        ]);
        return back();
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if ($post->user_id == Auth::id()) {
            $post->delete();
        }
        return back();
    }

    public function generateWithAI(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'visibility' => 'required|in:public,friends_only',
        ]);
        // Use PromptController's callAI method
        $promptController = app(\App\Http\Controllers\PromptController::class);
        $ai = $promptController->callAI($request->prompt);
        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $ai['text'],
            'visibility' => $request->visibility,
        ]);
        // Log prompt
        \App\Models\PromptLog::create([
            'user_id' => Auth::id(),
            'prompt' => $request->prompt,
            'response' => $ai['text'],
            'model_used' => $ai['model'],
            'latency_ms' => 0,
        ]);
        return back()->with('ai_response', $ai['text']);
    }
}
