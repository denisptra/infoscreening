<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Editor], true);
    }

    public function view(User $user, Article $article): bool
    {
        return $user->role === UserRole::Admin
            || ($user->role === UserRole::Editor
                && $article->user_id === $user->id
                && $article->status === 'draft');
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Editor], true);
    }

    public function update(User $user, Article $article): bool
    {
        return $user->role === UserRole::Admin
            || ($user->role === UserRole::Editor
                && $article->user_id === $user->id
                && $article->status === 'draft');
    }

    public function delete(User $user, Article $article): bool
    {
        return $this->update($user, $article);
    }
}
