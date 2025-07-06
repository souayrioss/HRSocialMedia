<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prompt',
        'response',
        'model_used',
        'latency_ms',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
