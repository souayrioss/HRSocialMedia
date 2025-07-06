@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-4">CivilEngineerConnect Profile</h2>
    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-4">
            <span class="font-semibold">Name:</span> {{ Auth::user()->name }}
        </div>
        <div class="mb-4">
            <span class="font-semibold">Email:</span> {{ Auth::user()->email }}
        </div>
        <div class="mb-4">
            <span class="font-semibold">Member since:</span> {{ Auth::user()->created_at->format('Y-m-d') }}
        </div>
        <div class="mb-4">
            <span class="font-semibold">Number of Posts:</span> {{ Auth::user()->posts()->count() }}
        </div>
        <div class="mb-4">
            <span class="font-semibold">Number of Friends:</span> {{ Auth::user()->friends()->count() }}
        </div>
        <div class="mb-4">
            <span class="font-semibold">AI Prompts Used:</span> {{ Auth::user()->promptLogs()->count() }}
        </div>
        <a href="{{ route('profile.edit') }}" class="bg-pink-700 text-white px-4 py-2 rounded hover:bg-pink-800">Edit Profile</a>
    </div>
</div>
@endsection
