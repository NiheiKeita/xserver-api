<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Repository;
use App\Models\Branch;
use App\Models\Commit;
use App\Models\ArticleVersion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
  /**
   * 記事一覧を表示
   */
  public function index(Repository $repository): Response
  {
    $articles = $repository->articles()
      ->with(['currentCommit', 'versions'])
      ->latest()
      ->paginate(20);

    return Inertia::render('Article/Index', [
      'repository' => $repository,
      'articles' => $articles,
    ]);
  }

  /**
   * 記事詳細を表示
   */
  public function show(Repository $repository, Article $article): Response
  {
    $article->load(['currentCommit', 'versions.branch', 'versions.commit.user', 'attachments']);

    return Inertia::render('Article/Show', [
      'repository' => $repository,
      'article' => $article,
    ]);
  }

  /**
   * 記事作成フォームを表示
   */
  public function create(Repository $repository): Response
  {
    $branches = $repository->branches;

    return Inertia::render('Article/Create', [
      'repository' => $repository,
      'branches' => $branches,
    ]);
  }

  /**
   * 記事を作成
   */
  public function store(Request $request, Repository $repository)
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

    return redirect()->route('repositories.articles.show', [$repository, $article])
      ->with('success', '記事が作成されました。');
  }

  /**
   * 記事編集フォームを表示
   */
  public function edit(Repository $repository, Article $article): Response
  {
    $this->authorize('update', $article);

    $article->load(['currentCommit', 'versions.branch']);
    $branches = $repository->branches;

    return Inertia::render('Article/Edit', [
      'repository' => $repository,
      'article' => $article,
      'branches' => $branches,
    ]);
  }

  /**
   * 記事を更新
   */
  public function update(Request $request, Repository $repository, Article $article)
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

    return redirect()->route('repositories.articles.show', [$repository, $article])
      ->with('success', '記事が更新されました。');
  }

  /**
   * 記事を削除
   */
  public function destroy(Repository $repository, Article $article)
  {
    $this->authorize('delete', $article);

    $article->delete();

    return redirect()->route('repositories.articles.index', $repository)
      ->with('success', '記事が削除されました。');
  }

  /**
   * 記事の履歴を表示
   */
  public function history(Repository $repository, Article $article): Response
  {
    $versions = $article->versions()
      ->with(['branch', 'commit.user'])
      ->latest()
      ->paginate(20);

    return Inertia::render('Article/History', [
      'repository' => $repository,
      'article' => $article,
      'versions' => $versions,
    ]);
  }

  /**
   * 特定のバージョンを表示
   */
  public function version(Repository $repository, Article $article, ArticleVersion $version): Response
  {
    $version->load(['branch', 'commit.user']);

    return Inertia::render('Article/Version', [
      'repository' => $repository,
      'article' => $article,
      'version' => $version,
    ]);
  }
}
