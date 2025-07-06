<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commit;
use App\Models\Repository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommitController extends Controller
{
  /**
   * コミット一覧を取得
   */
  public function index(Request $request, Repository $repository): JsonResponse
  {
    $commits = Commit::whereHas('articleVersions.article', function ($query) use ($repository) {
      $query->where('repository_id', $repository->id);
    })
      ->with(['user', 'articleVersions.article'])
      ->latest()
      ->paginate($request->get('per_page', 20));

    return response()->json($commits);
  }

  /**
   * コミット詳細を取得
   */
  public function show(Repository $repository, Commit $commit): JsonResponse
  {
    $commit->load(['user', 'articleVersions.article', 'articleVersions.branch']);

    return response()->json($commit);
  }

  /**
   * コミットを作成
   */
  public function store(Request $request, Repository $repository): JsonResponse
  {
    $validated = $request->validate([
      'message' => 'required|string|max:255',
      'article_id' => 'required|exists:articles,id',
      'branch_id' => 'required|exists:branches,id',
      'title' => 'required|string|max:255',
      'body' => 'required|string',
    ]);

    // コミットを作成
    $commit = Commit::create([
      'message' => $validated['message'],
      'user_id' => auth()->id(),
    ]);

    // 記事バージョンを作成
    $article = $repository->articles()->findOrFail($validated['article_id']);
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

    $commit->load(['user', 'articleVersions.article']);

    return response()->json($commit, 201);
  }
}
