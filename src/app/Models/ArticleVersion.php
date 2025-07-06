<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleVersion extends Model
{
    protected $fillable = [
        'article_id',
        'branch_id',
        'commit_id',
        'title',
        'body',
        'created_by',
    ];

    // リレーション
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function commit(): BelongsTo
    {
        return $this->belongsTo(Commit::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
