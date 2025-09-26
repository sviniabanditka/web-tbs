<?php

namespace App\Modules\Common\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Common\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }
}
