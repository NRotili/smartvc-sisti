<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SensoresPresione;
use Illuminate\Auth\Access\HandlesAuthorization;

class SensoresPresionePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SensoresPresione');
    }

    public function view(AuthUser $authUser, SensoresPresione $sensoresPresione): bool
    {
        return $authUser->can('View:SensoresPresione');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SensoresPresione');
    }

    public function update(AuthUser $authUser, SensoresPresione $sensoresPresione): bool
    {
        return $authUser->can('Update:SensoresPresione');
    }

    public function delete(AuthUser $authUser, SensoresPresione $sensoresPresione): bool
    {
        return $authUser->can('Delete:SensoresPresione');
    }

    public function restore(AuthUser $authUser, SensoresPresione $sensoresPresione): bool
    {
        return $authUser->can('Restore:SensoresPresione');
    }

    public function forceDelete(AuthUser $authUser, SensoresPresione $sensoresPresione): bool
    {
        return $authUser->can('ForceDelete:SensoresPresione');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SensoresPresione');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SensoresPresione');
    }

    public function replicate(AuthUser $authUser, SensoresPresione $sensoresPresione): bool
    {
        return $authUser->can('Replicate:SensoresPresione');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SensoresPresione');
    }

}