<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TipoCamaras;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoCamarasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TipoCamaras');
    }

    public function view(AuthUser $authUser, TipoCamaras $tipoCamaras): bool
    {
        return $authUser->can('View:TipoCamaras');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoCamaras');
    }

    public function update(AuthUser $authUser, TipoCamaras $tipoCamaras): bool
    {
        return $authUser->can('Update:TipoCamaras');
    }

    public function delete(AuthUser $authUser, TipoCamaras $tipoCamaras): bool
    {
        return $authUser->can('Delete:TipoCamaras');
    }

    public function restore(AuthUser $authUser, TipoCamaras $tipoCamaras): bool
    {
        return $authUser->can('Restore:TipoCamaras');
    }

    public function forceDelete(AuthUser $authUser, TipoCamaras $tipoCamaras): bool
    {
        return $authUser->can('ForceDelete:TipoCamaras');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoCamaras');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoCamaras');
    }

    public function replicate(AuthUser $authUser, TipoCamaras $tipoCamaras): bool
    {
        return $authUser->can('Replicate:TipoCamaras');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoCamaras');
    }

}