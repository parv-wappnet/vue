<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
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

    /**
     * @var array<string>
     */
    protected $appends = [
        'avatar_url',
    ];
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) return null;

        return config('filesystems.disks.s3.url') . '/' .
            (config('filesystems.disks.s3.folder_path') ? config('filesystems.disks.s3.folder_path') . '/' : '') .
            ltrim($this->avatar, '/');
    }

    public function sentFollowRequests()
    {
        return $this->hasMany(FollowRequest::class, 'sender_id');
    }

    public function receivedFollowRequests()
    {
        return $this->hasMany(FollowRequest::class, 'receiver_id');
    }

    // public function conversations()
    // {
    //     return $this->belongsToMany(Conversation::class, 'conversation_user', 'user_id', 'conversation_id')
    //         ->withTimestamps();
    // }
}
