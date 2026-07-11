<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any AuditLog');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AuditLog $auditlog): bool
    {
        return $user->checkPermissionTo('view AuditLog');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create AuditLog');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AuditLog $auditlog): bool
    {
        return $user->checkPermissionTo('update AuditLog');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AuditLog $auditlog): bool
    {
        return $user->checkPermissionTo('delete AuditLog');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any AuditLog');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AuditLog $auditlog): bool
    {
        return $user->checkPermissionTo('restore AuditLog');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any AuditLog');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, AuditLog $auditlog): bool
    {
        return $user->checkPermissionTo('replicate AuditLog');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder AuditLog');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AuditLog $auditlog): bool
    {
        return $user->checkPermissionTo('force-delete AuditLog');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any AuditLog');
    }
}
