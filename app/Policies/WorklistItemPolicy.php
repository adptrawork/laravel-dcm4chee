<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\WorklistItem;
use App\Models\User;

class WorklistItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any WorklistItem');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorklistItem $worklistitem): bool
    {
        return $user->checkPermissionTo('view WorklistItem');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create WorklistItem');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorklistItem $worklistitem): bool
    {
        return $user->checkPermissionTo('update WorklistItem');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorklistItem $worklistitem): bool
    {
        return $user->checkPermissionTo('delete WorklistItem');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any WorklistItem');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorklistItem $worklistitem): bool
    {
        return $user->checkPermissionTo('restore WorklistItem');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any WorklistItem');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, WorklistItem $worklistitem): bool
    {
        return $user->checkPermissionTo('replicate WorklistItem');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder WorklistItem');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorklistItem $worklistitem): bool
    {
        return $user->checkPermissionTo('force-delete WorklistItem');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any WorklistItem');
    }
}
