<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario de prueba (cliente) si no existe
        $customerOne = User::firstOrCreate(
            ['email' => 'cliente@test.cl'],
            [
                'name' => 'Juan Pérez',
                'user_type' => 'customer',
                'phone' => '+56 9 8765 4321',
                'rut' => '18.765.432-1',
                'password' => Hash::make('Test123!'),
                'email_verified_at' => now(),
            ]
        );

        $customerOne->syncRoles(['cliente']);

        // Crear otro usuario de prueba sin verificar
        $customerTwo = User::firstOrCreate(
            ['email' => 'maria@test.cl'],
            [
                'name' => 'María González',
                'user_type' => 'customer',
                'phone' => '+56 9 5555 1234',
                'rut' => '19.123.456-7',
                'password' => Hash::make('Test123!'),
                'email_verified_at' => null,
            ]
        );

        $customerTwo->syncRoles(['cliente']);

        $this->command->info('✅ Usuarios de prueba creados/verificados:');
        $this->command->info('   1. Email: cliente@test.cl | Password: Test123! | Verificado');
        $this->command->info('   2. Email: maria@test.cl | Password: Test123! | No verificado');
    }
}
