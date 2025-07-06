<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Repository;
use App\Models\PullRequest;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PullRequestController extends Controller
{
  /**
   * プルリクエスト一覧を表示
   */
  public function index(Repository $repository): Response
  {
    $pullRequests = $repository->pullRequests()
      ->with(['fromBranch', 'toBranch', 'comments.user'])
      ->latest()
      ->paginate(20);

    return Inertia::render('PullRequest/Index', [
      'repository' => $repository,
      'pullRequests' => $pullRequests,
    ]);
  }

  /**
   * プルリクエスト詳細を表示
   */
  public function show(Repository $repository, PullRequest $pullRequest): Response
  {
    $pullRequest->load([
      'fromBranch',
      'toBranch',
      'comments.user',
      'comments.pullRequest'
    ]);

    return Inertia::render('PullRequest/Show', [
      'repository' => $repository,
      'pullRequest' => $pullRequest,
    ]);
  }

  /**
   * プルリクエスト作成フォームを表示
   */
  public function create(Repository $repository): Response
  {
    $branches = $repository->branches;

    return Inertia::render('PullRequest/Create', [
      'repository' => $repository,
      'branches' => $branches,
    ]);
  }

  /**
   * プルリクエストを作成
   */
  public function store(Request $request, Repository $repository)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'from_branch_id' => 'required|exists:branches,id',
      'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
    ]);

    $pullRequest = $repository->pullRequests()->create([
      'from_branch_id' => $validated['from_branch_id'],
      'to_branch_id' => $validated['to_branch_id'],
      'title' => $validated['title'],
      'description' => $validated['description'],
      'status' => 'open',
    ]);

    return redirect()->route('repositories.pull-requests.show', [$repository, $pullRequest])
      ->with('success', 'プルリクエストが作成されました。');
  }

  /**
   * プルリクエストを更新
   */
  public function update(Request $request, Repository $repository, PullRequest $pullRequest)
  {
    $this->authorize('update', $pullRequest);

    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'status' => 'required|in:open,closed,merged',
    ]);

    $pullRequest->update($validated);

    return redirect()->route('repositories.pull-requests.show', [$repository, $pullRequest])
      ->with('success', 'プルリクエストが更新されました。');
  }

  /**
   * プルリクエストを削除
   */
  public function destroy(Repository $repository, PullRequest $pullRequest)
  {
    $this->authorize('delete', $pullRequest);

    $pullRequest->delete();

    return redirect()->route('repositories.pull-requests.index', $repository)
      ->with('success', 'プルリクエストが削除されました。');
  }

  /**
   * プルリクエストをマージ
   */
  public function merge(Repository $repository, PullRequest $pullRequest)
  {
    $this->authorize('merge', $pullRequest);

    if ($pullRequest->status !== 'open') {
      return back()->with('error', 'このプルリクエストはマージできません。');
    }

    // マージ処理を実行
    $pullRequest->update(['status' => 'merged']);

    return redirect()->route('repositories.pull-requests.show', [$repository, $pullRequest])
      ->with('success', 'プルリクエストがマージされました。');
  }

  /**
   * プルリクエストをクローズ
   */
  public function close(Repository $repository, PullRequest $pullRequest)
  {
    $this->authorize('update', $pullRequest);

    if ($pullRequest->status !== 'open') {
      return back()->with('error', 'このプルリクエストはクローズできません。');
    }

    $pullRequest->update(['status' => 'closed']);

    return redirect()->route('repositories.pull-requests.show', [$repository, $pullRequest])
      ->with('success', 'プルリクエストがクローズされました。');
  }
}
