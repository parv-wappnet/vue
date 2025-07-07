<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'admins',
        'members',
        'description',
        'name',
        'created_by',
    ];

    protected $casts = [
        'admins' => 'array',
        'members' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function conversations()
    {
        return $this->HasOne(Conversation::class, 'group_id');
    }
    // 🧠 Custom accessor to get admin users
    public function getAdminUsersAttribute()
    {
        return User::whereIn('id', $this->admins)->get();
    }

    // 🧠 Custom accessor to get member users
    public function getMemberUsersAttribute()
    {
        return User::whereIn('id', $this->members)->get();
    }
}
