<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $existing = User::where('email', 'kalenilucas061@gmail.com')->first();
        if ($existing) {
            return;
        }

        $password = Str::password(16);

        User::create([
            'name' => 'Chef K',
            'email' => 'kalenilucas061@gmail.com',
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->command?->info("Admin account created: kalenilucas061@gmail.com / {$password}");
        $this->command?->warn('Save this password now — it will not be shown again. Log in and set up two-factor authentication immediately.');
    }
}
