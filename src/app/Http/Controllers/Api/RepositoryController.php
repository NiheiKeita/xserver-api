<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RepositoryController extends Controller
{
    /**
     * リポジトリ一覧を取得
     */
    public function index(Request $request): JsonResponse
    {
        $repositories = Repository::with(['owner', 'users'])
            ->where('is_public', true)
            ->orWhereHas('users', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orWhere('owner_id', auth()->id())
            ->latest()
            ->paginate($request->get('per_page', 12));

        return response()->json($repositories);
    }

    /**
     * リポジトリ詳細を取得
     */
    public function show(Repository $repository): JsonResponse
    {
        $repository->load(['owner', 'users', 'branches', 'articles']);

        return response()->json($repository);
    }

    /**
     * リポジトリを作成
     */
    public function store(Request $request): JsonResponse
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

        $repository->load(['owner', 'branches']);

        return response()->json($repository, 201);
    }

    /**
     * リポジトリを更新
     */
    public function update(Request $request, Repository $repository): JsonResponse
    {
        $this->authorize('update', $repository);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'boolean',
        ]);

        $repository->update($validated);

        return response()->json($repository);
    }

    /**
     * リポジトリを削除
     */
    public function destroy(Repository $repository): JsonResponse
    {
        $this->authorize('delete', $repository);

        $repository->delete();

        return response()->json(['message' => 'リポジトリが削除されました。']);
    }

    /**
     * リポジトリにユーザーを追加
     */
    public function addUser(Request $request, Repository $repository): JsonResponse
    {
        $this->authorize('manageUsers', $repository);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:read,write,admin',
        ]);

        $repository->users()->attach($validated['user_id'], [
            'role' => $validated['role'],
        ]);

        return response()->json(['message' => 'ユーザーが追加されました。']);
    }

    /**
     * リポジトリからユーザーを削除
     */
    public function removeUser(Request $request, Repository $repository): JsonResponse
    {
        $this->authorize('manageUsers', $repository);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $repository->users()->detach($validated['user_id']);

        return response()->json(['message' => 'ユーザーが削除されました。']);
    }
}
