<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, Project $project): bool
    {
        return (int) $project->user_id === (int) $user->id;
    }

    public function uploadFiles(User $user, Project $project): bool
    {
        // FE: upload solo se è suo e NON è chiuso
        return $this->view($user, $project) && $project->status !== 'closed';
    }

    public function comment(User $user, Project $project): bool
    {
        // FE: commenti solo se è suo e NON è chiuso
        return $this->view($user, $project) && $project->status !== 'closed';
    }

    public function manage(User $user, Project $project): bool
    {
        return method_exists($user, 'isAdmin') && $user->isAdmin();
    }

    public function viewAny(User $user): bool
    {
        // per index BO (se mai usi authorize('viewAny', Project::class))
        return method_exists($user, 'isAdmin') && $user->isAdmin();
    }
}