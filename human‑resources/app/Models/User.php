<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function friendships()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', 'accepted');
    }

    public function promptLogs()
    {
        return $this->hasMany(PromptLog::class);
    }

    public function allFriends()
    {
        $userId = $this->id;
        $friendIds = \App\Models\Friendship::where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('friend_id', $userId);
        })->where('status', 'accepted')->get()
          ->map(function($friendship) use ($userId) {
              return $friendship->user_id == $userId ? $friendship->friend_id : $friendship->user_id;
          })->unique()->toArray();

        return self::whereIn('id', $friendIds)->get();
    }
}
