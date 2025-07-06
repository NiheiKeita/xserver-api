<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = [
        'repository_id',
        'current_commit_id',
    ];

    // リレーション
    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    public function currentCommit(): BelongsTo
    {
        return $this->belongsTo(Commit::class, 'current_commit_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ArticleVersion::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function latestVersion(): BelongsTo
    {
        return $this->belongsTo(ArticleVersion::class, 'current_commit_id', 'commit_id');
    }
}
