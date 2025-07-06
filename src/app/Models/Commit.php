<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commit extends Model
{
    protected $fillable = [
        'message',
        'user_id',
    ];

    // リレーション
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function articleVersions(): HasMany
    {
        return $this->hasMany(ArticleVersion::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'current_commit_id');
    }
}
