<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RepositoryController extends Controller
{
  /**
   * リポジトリ一覧を表示
   */
  public function index(): Response
  {
    $repositories = Repository::with(['owner', 'users'])
      ->where('is_public', true)
      ->orWhereHas('users', function ($query) {
        $query->where('user_id', auth()->id());
      })
      ->orWhere('owner_id', auth()->id())
      ->latest()
      ->paginate(12);

    return Inertia::render('Repository/Index', [
      'repositories' => $repositories,
    ]);
  }

  /**
   * リポジトリ詳細を表示
   */
  public function show(Repository $repository): Response
  {
    $repository->load(['owner', 'users', 'branches', 'articles']);

    return Inertia::render('Repository/Show', [
      'repository' => $repository,
    ]);
  }

  /**
   * リポジトリ作成フォームを表示
   */
  public function create(): Response
  {
    return Inertia::render('Repository/Create');
  }

  /**
   * リポジトリを作成
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'is_public' => 'boolean',
    ]);

    $repository = Repository::create([
      'name' => $validated['name'],
      'owner_id' => auth()->id(),
      'is_public' => $validated['is_public'] ?? false,
    ]);

    // デフォルトブランチ（main）を作成
    $repository->branches()->create([
      'name' => 'main',
      'base_branch_id' => null,
    ]);

    return redirect()->route('repositories.show', $repository)
      ->with('success', 'リポジトリが作成されました。');
  }

  /**
   * リポジトリ編集フォームを表示
   */
  public function edit(Repository $repository): Response
  {
    $this->authorize('update', $repository);

    return Inertia::render('Repository/Edit', [
      'repository' => $repository,
    ]);
  }

  /**
   * リポジトリを更新
   */
  public function update(Request $request, Repository $repository)
  {
    $this->authorize('update', $repository);

    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'is_public' => 'boolean',
    ]);

    $repository->update($validated);

    return redirect()->route('repositories.show', $repository)
      ->with('success', 'リポジトリが更新されました。');
  }

  /**
   * リポジトリを削除
   */
  public function destroy(Repository $repository)
  {
    $this->authorize('delete', $repository);

    $repository->delete();

    return redirect()->route('repositories.index')
      ->with('success', 'リポジトリが削除されました。');
  }
}
