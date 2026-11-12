<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Editor = 'editor';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Editor => 'Editor',
            self::Member => 'Pembaca',
        };
    }

    public function canAccessCms(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin, self::Editor], true);
    }
}
