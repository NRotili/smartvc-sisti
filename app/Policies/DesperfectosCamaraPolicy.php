<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DesperfectosCamara;
use Illuminate\Auth\Access\HandlesAuthorization;

class DesperfectosCamaraPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DesperfectosCamara');
    }

    public function view(AuthUser $authUser, DesperfectosCamara $desperfectosCamara): bool
    {
        return $authUser->can('View:DesperfectosCamara');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DesperfectosCamara');
    }

    public function update(AuthUser $authUser, DesperfectosCamara $desperfectosCamara): bool
    {
        return $authUser->can('Update:DesperfectosCamara');
    }

    public function delete(AuthUser $authUser, DesperfectosCamara $desperfectosCamara): bool
    {
        return $authUser->can('Delete:DesperfectosCamara');
    }

    public function restore(AuthUser $authUser, DesperfectosCamara $desperfectosCamara): bool
    {
        return $authUser->can('Restore:DesperfectosCamara');
    }

    public function forceDelete(AuthUser $authUser, DesperfectosCamara $desperfectosCamara): bool
    {
        return $authUser->can('ForceDelete:DesperfectosCamara');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DesperfectosCamara');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DesperfectosCamara');
    }

    public function replicate(AuthUser $authUser, DesperfectosCamara $desperfectosCamara): bool
    {
        return $authUser->can('Replicate:DesperfectosCamara');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DesperfectosCamara');
    }

}