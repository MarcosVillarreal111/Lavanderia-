<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'make:admin {email} {password} {name?}';

    protected $description = 'Crea o actualiza un usuario con rol admin';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name') ?? 'Administrador';

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'email_verified_at' => now(),
            ]);
        } else {
            $user->update([
                'name' => $name,
                'password' => bcrypt($password),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }

        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        $this->info("Usuario admin: {$user->email}");

        return 0;
    }
}
