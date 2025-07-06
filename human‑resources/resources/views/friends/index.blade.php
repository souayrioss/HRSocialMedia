@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-4">Friends</h2>
    <div class="mb-6">
        <form method="GET" action="{{ route('friends.index') }}">
            <input type="text" name="search" placeholder="Search users..." class="border rounded p-2 w-1/2" value="{{ request('search') }}">
            <button type="submit" class="bg-pink-700 text-white px-3 py-1 rounded">Search</button>
        </form>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($users as $user)
            <div class="bg-white shadow rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    <div>
                        <div class="font-semibold">{{ $user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                    </div>
                </div>
                @php
                    $alreadyFriend = collect($friends)->contains(function($f) use ($user) { return $f && $f->id === $user->id; });
                    $pending = \App\Models\Friendship::where(function($q) use ($user) {
                        $q->where('user_id', Auth::id())->where('friend_id', $user->id);
                    })->orWhere(function($q) use ($user) {
                        $q->where('user_id', $user->id)->where('friend_id', Auth::id());
                    })->where('status', 'pending')->exists();
                @endphp
                @if(!$alreadyFriend && !$pending)
                <form method="POST" action="{{ route('friends.send', $user->id) }}">
                    @csrf
                    <button class="bg-pink-600 text-white px-3 py-1 rounded">Add Friend</button>
                </form>
                @elseif($pending)
                    <span class="text-yellow-600 font-semibold">Pending</span>
                @elseif($alreadyFriend)
                    <span class="text-green-600 font-semibold">Friend</span>
                @endif
            </div>
        @endforeach
    </div>
    <h3 class="text-xl font-semibold mt-8 mb-2">Pending Friend Requests</h3>
    <div class="space-y-2 mb-8">
        @foreach($pendingRequests as $request)
            <div class="bg-yellow-50 border-l-4 border-yellow-600 p-3 flex items-center justify-between">
                <span>{{ $request->user->name }}</span>
                <div>
                    <form method="POST" action="{{ route('friends.accept', $request->id) }}" class="inline">
                        @csrf
                        <button class="bg-green-600 text-white px-2 py-1 rounded">Accept</button>
                    </form>
                    <form method="POST" action="{{ route('friends.decline', $request->id) }}" class="inline">
                        @csrf
                        <button class="bg-red-600 text-white px-2 py-1 rounded">Decline</button>
                    </form>
                </div>
            </div>
        @endforeach
        @if($pendingRequests->isEmpty())
            <div class="text-gray-500">No pending requests.</div>
        @endif
    </div>
    <h3 class="text-xl font-semibold mb-2">My Friends List</h3>
    <div class="space-y-2">
        @foreach($friends as $friend)
            @if($friend)
            <div class="bg-pink-50 border-l-4 border-pink-700 p-3 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">{{ strtoupper(substr($friend->name,0,1)) }}</div>
                    <span>{{ $friend->name }}</span>
                </div>
                <form method="POST" action="{{ route('friends.unfriend', $friend->id) }}">
                    @csrf
                    <button class="text-red-600 hover:underline">Unfriend</button>
                </form>
            </div>
            @endif
        @endforeach
        @if(collect($friends)->isEmpty())
            <div class="text-gray-500">No friends yet.</div>
        @endif
    </div>
</div>
@endsection
