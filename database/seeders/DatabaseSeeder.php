<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate('admin');
        Role::findOrCreate('cliente');

        $admin = User::updateOrCreate(
            ['email' => 'admin@espuma.test'],
            [
                'name' => 'Administrador Espuma',
                'password' => Hash::make('password'),
                'whatsapp' => '5215500000000',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $cliente = User::updateOrCreate(
            ['email' => 'cliente@espuma.test'],
            [
                'name' => 'Cliente Demo',
                'password' => Hash::make('password'),
                'whatsapp' => '5215511111111',
                'email_verified_at' => now(),
            ]
        );
        $cliente->syncRoles(['cliente']);
    }
}
