<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Lấy thông tin admin thực hiện hành động
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Lấy model bị tác động (polymorphic)
     */
    public function subject()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Helper method để ghi log nhanh
     */
    public static function log(
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return self::create([
            'admin_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Lấy màu badge theo loại action
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'create' => 'bg-green-100 text-green-700',
            'update' => 'bg-blue-100 text-blue-700',
            'delete' => 'bg-red-100 text-red-700',
            'login' => 'bg-indigo-100 text-indigo-700',
            'logout' => 'bg-gray-100 text-gray-700',
            'export' => 'bg-yellow-100 text-yellow-700',
            'approve' => 'bg-emerald-100 text-emerald-700',
            'reject' => 'bg-orange-100 text-orange-700',
            'restore' => 'bg-cyan-100 text-cyan-700',
            'force_delete' => 'bg-rose-100 text-rose-700',
            'cleanup' => 'bg-amber-100 text-amber-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Lấy icon theo loại action
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'create' => 'fa-plus-circle',
            'update' => 'fa-edit',
            'delete' => 'fa-trash',
            'login' => 'fa-sign-in-alt',
            'logout' => 'fa-sign-out-alt',
            'export' => 'fa-file-export',
            'approve' => 'fa-check-circle',
            'reject' => 'fa-times-circle',
            'restore' => 'fa-undo',
            'force_delete' => 'fa-skull-crossbones',
            'cleanup' => 'fa-broom',
            default => 'fa-circle',
        };
    }
}
