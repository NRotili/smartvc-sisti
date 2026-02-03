<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Servidores;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServidoresPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Servidores');
    }

    public function view(AuthUser $authUser, Servidores $servidores): bool
    {
        return $authUser->can('View:Servidores');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Servidores');
    }

    public function update(AuthUser $authUser, Servidores $servidores): bool
    {
        return $authUser->can('Update:Servidores');
    }

    public function delete(AuthUser $authUser, Servidores $servidores): bool
    {
        return $authUser->can('Delete:Servidores');
    }

    public function restore(AuthUser $authUser, Servidores $servidores): bool
    {
        return $authUser->can('Restore:Servidores');
    }

    public function forceDelete(AuthUser $authUser, Servidores $servidores): bool
    {
        return $authUser->can('ForceDelete:Servidores');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Servidores');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Servidores');
    }

    public function replicate(AuthUser $authUser, Servidores $servidores): bool
    {
        return $authUser->can('Replicate:Servidores');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Servidores');
    }

}