<?php

namespace App\Policies;

use App\Models\Repository;
use App\Models\User;

class RepositoryPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {
    return true;
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, Repository $repository): bool
  {
    // 公開リポジトリは誰でも閲覧可能
    if ($repository->is_public) {
      return true;
    }

    // オーナーは閲覧可能
    if ($repository->owner_id === $user->id) {
      return true;
    }

    // リポジトリのメンバーは閲覧可能
    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user): bool
  {
    return true;
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, Repository $repository): bool
  {
    return $repository->owner_id === $user->id;
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, Repository $repository): bool
  {
    return $repository->owner_id === $user->id;
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, Repository $repository): bool
  {
    return $repository->owner_id === $user->id;
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, Repository $repository): bool
  {
    return $repository->owner_id === $user->id;
  }
}
