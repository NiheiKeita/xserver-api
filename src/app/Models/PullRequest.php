<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PullRequest extends Model
{
    protected $fillable = [
        'repository_id',
        'from_branch_id',
        'to_branch_id',
        'title',
        'description',
        'status',
        'created_by',
        'merged_by',
        'merged_at',
    ];

    protected $casts = [
        'merged_at' => 'datetime',
    ];

    // リレーション
    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mergedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merged_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PrComment::class);
    }
}
