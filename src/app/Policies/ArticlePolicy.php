<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use App\Models\Repository;

class ArticlePolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user, Repository $repository): bool
  {
    // リポジトリの閲覧権限をチェック
    if ($repository->is_public) {
      return true;
    }

    if ($repository->owner_id === $user->id) {
      return true;
    }

    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, Article $article): bool
  {
    $repository = $article->repository;

    // リポジトリの閲覧権限をチェック
    if ($repository->is_public) {
      return true;
    }

    if ($repository->owner_id === $user->id) {
      return true;
    }

    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user, Repository $repository): bool
  {
    // リポジトリの編集権限をチェック
    if ($repository->owner_id === $user->id) {
      return true;
    }

    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, Article $article): bool
  {
    $repository = $article->repository;

    // リポジトリの編集権限をチェック
    if ($repository->owner_id === $user->id) {
      return true;
    }

    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, Article $article): bool
  {
    $repository = $article->repository;

    // リポジトリの編集権限をチェック
    if ($repository->owner_id === $user->id) {
      return true;
    }

    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, Article $article): bool
  {
    $repository = $article->repository;

    if ($repository->owner_id === $user->id) {
      return true;
    }

    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, Article $article): bool
  {
    $repository = $article->repository;

    return $repository->owner_id === $user->id;
  }
}
