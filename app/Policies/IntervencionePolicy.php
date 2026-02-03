<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Intervencione;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntervencionePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Intervencione');
    }

    public function view(AuthUser $authUser, Intervencione $intervencione): bool
    {
        return $authUser->can('View:Intervencione');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Intervencione');
    }

    public function update(AuthUser $authUser, Intervencione $intervencione): bool
    {
        return $authUser->can('Update:Intervencione');
    }

    public function delete(AuthUser $authUser, Intervencione $intervencione): bool
    {
        return $authUser->can('Delete:Intervencione');
    }

    public function restore(AuthUser $authUser, Intervencione $intervencione): bool
    {
        return $authUser->can('Restore:Intervencione');
    }

    public function forceDelete(AuthUser $authUser, Intervencione $intervencione): bool
    {
        return $authUser->can('ForceDelete:Intervencione');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Intervencione');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Intervencione');
    }

    public function replicate(AuthUser $authUser, Intervencione $intervencione): bool
    {
        return $authUser->can('Replicate:Intervencione');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Intervencione');
    }

}