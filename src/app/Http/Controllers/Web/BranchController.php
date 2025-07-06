<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Repository;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
  /**
   * ブランチ一覧を表示
   */
  public function index(Repository $repository): Response
  {
    $branches = $repository->branches()
      ->with(['baseBranch', 'articleVersions'])
      ->latest()
      ->get();

    return Inertia::render('Branch/Index', [
      'repository' => $repository,
      'branches' => $branches,
    ]);
  }

  /**
   * ブランチ詳細を表示
   */
  public function show(Repository $repository, Branch $branch): Response
  {
    $branch->load(['baseBranch', 'childBranches', 'articleVersions.article', 'articleVersions.commit.user']);

    return Inertia::render('Branch/Show', [
      'repository' => $repository,
      'branch' => $branch,
    ]);
  }

  /**
   * ブランチ作成フォームを表示
   */
  public function create(Repository $repository): Response
  {
    $branches = $repository->branches;

    return Inertia::render('Branch/Create', [
      'repository' => $repository,
      'branches' => $branches,
    ]);
  }

  /**
   * ブランチを作成
   */
  public function store(Request $request, Repository $repository)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'base_branch_id' => 'nullable|exists:branches,id',
    ]);

    $branch = $repository->branches()->create([
      'name' => $validated['name'],
      'base_branch_id' => $validated['base_branch_id'],
    ]);

    return redirect()->route('repositories.branches.show', [$repository, $branch])
      ->with('success', 'ブランチが作成されました。');
  }

  /**
   * ブランチを削除
   */
  public function destroy(Repository $repository, Branch $branch)
  {
    // mainブランチは削除不可
    if ($branch->name === 'main') {
      return back()->with('error', 'mainブランチは削除できません。');
    }

    // 子ブランチがある場合は削除不可
    if ($branch->childBranches()->exists()) {
      return back()->with('error', '子ブランチが存在するため削除できません。');
    }

    $branch->delete();

    return redirect()->route('repositories.branches.index', $repository)
      ->with('success', 'ブランチが削除されました。');
  }
}
