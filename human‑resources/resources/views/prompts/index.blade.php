@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-8">
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="POST" action="{{ route('prompts.store') }}">
            @csrf
            <textarea name="prompt" rows="2" class="w-full border rounded p-2 mb-2" placeholder="Ask the AI about civil engineering..."></textarea>
            <button type="submit" class="bg-pink-700 text-white px-4 py-2 rounded hover:bg-pink-800">Generate Post</button>
        </form>
        @if(session('ai_response'))
            <div class="mt-4 p-3 bg-pink-50 border-l-4 border-pink-700 text-pink-900">
                <strong>AI Response:</strong> {{ session('ai_response') }}
            </div>
        @endif
    </div>
    <h2 class="text-lg font-semibold mb-4">Prompt History</h2>
    <div class="space-y-4">
        @foreach($logs as $log)
            <div class="bg-white shadow rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="text-xs text-gray-500 mb-1">{{ $log->created_at->format('Y-m-d H:i') }} | Model: {{ $log->model_used }} | {{ $log->latency_ms }}ms</div>
                    <div class="font-semibold">Prompt:</div>
                    <div class="mb-2">{{ $log->prompt }}</div>
                    <div class="font-semibold">Response:</div>
                    <div>{{ $log->response }}</div>
                </div>
                <form method="GET" action="{{ route('feed.index') }}" class="mt-2 md:mt-0">
                    <input type="hidden" name="prompt" value="{{ $log->prompt }}">
                    <button type="submit" class="ml-4 bg-pink-600 text-white px-3 py-1 rounded">Reuse</button>
                </form>
            </div>
        @endforeach
        @if($logs->isEmpty())
            <div class="text-center text-gray-500">No prompts yet.</div>
        @endif
    </div>
</div>
@endsection
