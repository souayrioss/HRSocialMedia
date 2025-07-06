<?php

namespace App\Http\Controllers;

use App\Models\PromptLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PromptController extends Controller
{
    public function index()
    {
        $logs = PromptLog::where('user_id', Auth::id())->latest()->get();
        return view('prompts.index', compact('logs'));
    }

    public function store(Request $request)
    {
        // ✅ Extend script execution time
        set_time_limit(180);

        $request->validate([
            'prompt' => 'required|string',
        ]);

        $start = microtime(true);
        $response = $this->callAI($request->prompt);
        $latency = (int)((microtime(true) - $start) * 1000);

        // Handle errors from Gemini
        if (str_starts_with($response['text'], 'Error:')) {
            return back()->withErrors(['prompt' => $response['text']]);
        }

        // Save to DB
        PromptLog::create([
            'user_id'     => Auth::id(),
            'prompt'      => $request->prompt,
            'response'    => $response['text'],
            'model_used'  => $response['model'],
            'latency_ms'  => $latency,
            // 'token_count' => $response['token_count'] ?? 0,
        ]);

        return back()->with('ai_response', $response['text']);
    }

    // 🔄 Public so it can be reused from other controllers (e.g. PostController)
    public function callAI($prompt)
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = 'gemini-2.5-pro';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $responseText = 'No response';
        $tokenCount = 0;

        try {
            $httpResponse = Http::timeout(180)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                ]);

            $data = $httpResponse->json();

            // 🔍 Log response (for debug)
            Log::info('Gemini API response', $data);

            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $responseText = $data['candidates'][0]['content']['parts'][0]['text'];
            } else {
                $responseText = 'No valid response from Gemini.';
            }

            $tokenCount = $data['usageMetadata']['totalTokenCount'] ?? 0;

        } catch (\Exception $e) {
            Log::error('Gemini API error: ' . $e->getMessage());
            $responseText = 'Error: ' . $e->getMessage();
        }

        return [
            'text'        => $responseText,
            'model'       => $model,
            'token_count' => $tokenCount,
        ];
    }
}
