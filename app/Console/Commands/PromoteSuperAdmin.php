<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:promote-super-admin {email : Email pengguna yang akan diberi akses Super Admin}')]
#[Description('Berikan akses awal Super Admin kepada pengguna yang sudah terdaftar')]
class PromoteSuperAdmin extends Command
{
    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->components->error('Pengguna tidak ditemukan. Daftarkan akun tersebut terlebih dahulu.');

            return self::FAILURE;
        }

        $user->forceFill([
            'role' => UserRole::SuperAdmin,
            'is_admin' => true,
        ])->save();

        $this->components->info("{$user->email} sekarang memiliki peran Super Admin.");

        return self::SUCCESS;
    }
}
