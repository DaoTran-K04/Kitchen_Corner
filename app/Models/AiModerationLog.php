<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiModerationLog extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'source',
        'severity',
        'intent',
        'blocked_content',
        'excerpt',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
