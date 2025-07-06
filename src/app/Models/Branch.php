<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'repository_id',
        'name',
        'base_branch_id',
        'created_by',
    ];

    // リレーション
    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    public function baseBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'base_branch_id');
    }

    public function childBranches(): HasMany
    {
        return $this->hasMany(Branch::class, 'base_branch_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function articleVersions(): HasMany
    {
        return $this->hasMany(ArticleVersion::class);
    }

    public function pullRequestsFrom(): HasMany
    {
        return $this->hasMany(PullRequest::class, 'from_branch_id');
    }

    public function pullRequestsTo(): HasMany
    {
        return $this->hasMany(PullRequest::class, 'to_branch_id');
    }
}
