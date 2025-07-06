<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Repository;
use App\Models\Commit;
use App\Models\ArticleVersion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    /**
     * 記事一覧を取得
     */
    public function index(Request $request, Repository $repository): JsonResponse
    {
        $articles = $repository->articles()
            ->with(['currentCommit', 'versions'])
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json($articles);
    }

    /**
     * 記事詳細を取得
     */
    public function show(Repository $repository, Article $article): JsonResponse
    {
        $article->load(['currentCommit', 'versions.branch', 'versions.commit.user', 'attachments']);

        return response()->json($article);
    }

    /**
     * 記事を作成
     */
    public function store(Request $request, Repository $repository): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'commit_message' => 'required|string|max:255',
        ]);

        // コミットを作成
        $commit = Commit::create([
            'message' => $validated['commit_message'],
            'user_id' => auth()->id(),
        ]);

        // 記事を作成
        $article = $repository->articles()->create([
            'current_commit_id' => $commit->id,
        ]);

        // 記事バージョンを作成
        $article->versions()->create([
            'branch_id' => $validated['branch_id'],
            'commit_id' => $commit->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        $article->load(['currentCommit', 'versions.branch']);

        return response()->json($article, 201);
    }

    /**
     * 記事を更新
     */
    public function update(Request $request, Repository $repository, Article $article): JsonResponse
    {
        $this->authorize('update', $article);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'commit_message' => 'required|string|max:255',
        ]);

        // コミットを作成
        $commit = Commit::create([
            'message' => $validated['commit_message'],
            'user_id' => auth()->id(),
        ]);

        // 記事バージョンを作成
        $article->versions()->create([
            'branch_id' => $validated['branch_id'],
            'commit_id' => $commit->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        // 記事の現在のコミットを更新
        $article->update([
            'current_commit_id' => $commit->id,
        ]);

        $article->load(['currentCommit', 'versions.branch']);

        return response()->json($article);
    }

    /**
     * 記事を削除
     */
    public function destroy(Repository $repository, Article $article): JsonResponse
    {
        $this->authorize('delete', $article);

        $article->delete();

        return response()->json(['message' => '記事が削除されました。']);
    }

    /**
     * 記事の履歴を取得
     */
    public function history(Request $request, Repository $repository, Article $article): JsonResponse
    {
        $versions = $article->versions()
            ->with(['branch', 'commit.user'])
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json($versions);
    }

    /**
     * 特定のバージョンを取得
     */
    public function version(Repository $repository, Article $article, ArticleVersion $version): JsonResponse
    {
        $version->load(['branch', 'commit.user']);

        return response()->json($version);
    }
}
