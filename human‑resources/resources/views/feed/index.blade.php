@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-8">
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="POST" action="{{ route('posts.generate') }}">
            @csrf
            <textarea name="prompt" rows="2" class="w-full border rounded p-2 mb-2" placeholder="Generate a post with AI...">{{ request('prompt') }}</textarea>
            <div class="flex items-center justify-between">
                <select name="visibility" class="border rounded p-1">
                    <option value="public">Public</option>
                    <option value="friends_only">Friends Only</option>
                </select>
                <button type="submit" class="bg-pink-700 text-white px-4 py-2 rounded hover:bg-pink-800">Generate Post</button>
            </div>
        </form>
        @if(session('ai_response'))
            <div class="mt-4 p-3 bg-pink-50 border-l-4 border-pink-700 text-pink-900">
                <strong>AI Response:</strong> {{ session('ai_response') }}
            </div>
        @endif
    </div>
    <div class="flex justify-between mb-4">
        <a href="{{ route('feed.index', ['filter' => 'all']) }}" class="{{ request('filter', 'all') == 'all' ? 'font-bold text-pink-700' : '' }}">All Posts</a>
        <a href="{{ route('feed.index', ['filter' => 'mine']) }}" class="{{ request('filter') == 'mine' ? 'font-bold text-pink-700' : '' }}">My Posts</a>
        <a href="{{ route('feed.index', ['filter' => 'friends']) }}" class="{{ request('filter') == 'friends' ? 'font-bold text-pink-700' : '' }}">My Friends' Posts</a>
    </div>
    @foreach($posts as $post)
        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <div class="flex items-center mb-2">
                <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">{{ strtoupper(substr($post->user->name,0,1)) }}</div>
                <div>
                    <span class="font-semibold">{{ $post->user->name }}</span>
                    <span class="text-xs text-gray-500 ml-2">{{ $post->created_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="mb-2">{{ $post->content }}</div>
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>{{ ucfirst($post->visibility) }}</span>
                @if($post->user_id == auth()->id())
                <form method="POST" action="{{ route('posts.destroy', $post->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                </form>
                @endif
            </div>
        </div>
    @endforeach
    @if($posts->isEmpty())
        <div class="text-center text-gray-500">No posts to show.</div>
    @endif
</div>
@endsection
