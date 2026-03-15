<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Badge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Các thử thách cho badge này
     */
    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    /**
     * Users đã nhận badge này
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('earned_at', 'expires_at')
            ->withTimestamps();
    }

    /**
     * User badges records
     */
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }
}
