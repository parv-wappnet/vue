<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return $this->hasMany(Conversation::class, 'group_id');
    }
}
