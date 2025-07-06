<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $friends = $user->allFriends(); // Use new allFriends method
        $pendingRequests = Friendship::where('friend_id', $user->id)->where('status', 'pending')->get();
        $users = User::where('id', '!=', $user->id)->get();
        return view('friends.index', compact('friends', 'pendingRequests', 'users'));
    }

    public function sendRequest(Request $request, $friend_id)
    {
        $user = Auth::user();
        if ($user->id == $friend_id) return back();
        Friendship::firstOrCreate([
            'user_id' => $user->id,
            'friend_id' => $friend_id
        ], [
            'status' => 'pending',
            'only_share_with' => false
        ]);
        return back();
    }

    public function acceptRequest($id)
    {
        $friendship = Friendship::findOrFail($id);
        if ($friendship->friend_id == Auth::id()) {
            $friendship->status = 'accepted';
            $friendship->save();
        }
        return back();
    }

    public function declineRequest($id)
    {
        $friendship = Friendship::findOrFail($id);
        if ($friendship->friend_id == Auth::id()) {
            $friendship->delete();
        }
        return back();
    }

    public function unfriend($id)
    {
        $friendship = Friendship::findOrFail($id);
        if ($friendship->user_id == Auth::id() || $friendship->friend_id == Auth::id()) {
            $friendship->delete();
        }
        return back();
    }
}
