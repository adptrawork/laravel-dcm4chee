<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\WorklistItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorklistItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WorklistItem');
    }

    public function view(AuthUser $authUser, WorklistItem $worklistItem): bool
    {
        return $authUser->can('View:WorklistItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WorklistItem');
    }

    public function update(AuthUser $authUser, WorklistItem $worklistItem): bool
    {
        return $authUser->can('Update:WorklistItem');
    }

    public function delete(AuthUser $authUser, WorklistItem $worklistItem): bool
    {
        return $authUser->can('Delete:WorklistItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WorklistItem');
    }

    public function restore(AuthUser $authUser, WorklistItem $worklistItem): bool
    {
        return $authUser->can('Restore:WorklistItem');
    }

    public function forceDelete(AuthUser $authUser, WorklistItem $worklistItem): bool
    {
        return $authUser->can('ForceDelete:WorklistItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WorklistItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WorklistItem');
    }

    public function replicate(AuthUser $authUser, WorklistItem $worklistItem): bool
    {
        return $authUser->can('Replicate:WorklistItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WorklistItem');
    }

}