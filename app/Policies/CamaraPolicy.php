<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Camara;
use Illuminate\Auth\Access\HandlesAuthorization;

class CamaraPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Camara');
    }

    public function view(AuthUser $authUser, Camara $camara): bool
    {
        return $authUser->can('View:Camara');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Camara');
    }

    public function update(AuthUser $authUser, Camara $camara): bool
    {
        return $authUser->can('Update:Camara');
    }

    public function delete(AuthUser $authUser, Camara $camara): bool
    {
        return $authUser->can('Delete:Camara');
    }

    public function restore(AuthUser $authUser, Camara $camara): bool
    {
        return $authUser->can('Restore:Camara');
    }

    public function forceDelete(AuthUser $authUser, Camara $camara): bool
    {
        return $authUser->can('ForceDelete:Camara');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Camara');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Camara');
    }

    public function replicate(AuthUser $authUser, Camara $camara): bool
    {
        return $authUser->can('Replicate:Camara');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Camara');
    }

}