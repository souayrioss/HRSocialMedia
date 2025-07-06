<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $filter = request('filter', 'all');
        // Get all accepted friendships where the user is either user_id or friend_id
        $friendships = \App\Models\Friendship::where(function($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('friend_id', $user->id);
        })->where('status', 'accepted')->get();
        // Get the IDs of the user's friends
        $friendIds = $friendships->map(function($friendship) use ($user) {
            return $friendship->user_id == $user->id ? $friendship->friend_id : $friendship->user_id;
        })->unique()->values()->toArray();

        if ($filter === 'mine') {
            $posts = \App\Models\Post::where('user_id', $user->id)->latest()->get();
        } elseif ($filter === 'friends') {
            $posts = \App\Models\Post::whereIn('user_id', $friendIds)->where('visibility', 'friends_only')->latest()->get();
        } else {
            $posts = \App\Models\Post::where(function($q) use ($user, $friendIds) {
                $q->where('user_id', $user->id)
                  ->orWhere(function($q2) use ($friendIds) {
                      $q2->whereIn('user_id', $friendIds)->where('visibility', 'friends_only');
                  })
                  ->orWhere('visibility', 'public');
            })->latest()->get();
        }
        return view('feed.index', compact('posts', 'filter'));
    }
}
