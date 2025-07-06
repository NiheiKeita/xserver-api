<?php

namespace App\Policies;

use App\Models\PullRequest;
use App\Models\User;
use App\Models\Repository;

class PullRequestPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user, Repository $repository): bool
  {
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
  public function view(User $user, PullRequest $pullRequest): bool
  {
    $repository = $pullRequest->repository;

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
    if ($repository->owner_id === $user->id) {
      return true;
    }

    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, PullRequest $pullRequest): bool
  {
    $repository = $pullRequest->repository;

    if ($repository->owner_id === $user->id) {
      return true;
    }

    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, PullRequest $pullRequest): bool
  {
    $repository = $pullRequest->repository;

    if ($repository->owner_id === $user->id) {
      return true;
    }

    return $repository->users()->where('user_id', $user->id)->exists();
  }

  /**
   * Determine whether the user can merge the model.
   */
  public function merge(User $user, PullRequest $pullRequest): bool
  {
    $repository = $pullRequest->repository;

    // リポジトリのオーナーのみマージ可能
    return $repository->owner_id === $user->id;
  }
}
