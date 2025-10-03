<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    protected $signature = 'user:create-test';
    protected $description = 'Create a test user for authentication';

    public function handle()
    {
        $user = User::firstOrCreate(
            ['email' => 'john.adrian.bacon@gmail.com'],
            [
                'name' => 'John Adrian Bacon',
                'email' => 'john.adrian.bacon@gmail.com',
                'password' => Hash::make('Facebook.com2025'),
                'email_verified_at' => now(),
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->info('User created successfully!');
        } else {
            $this->info('User already exists!');
        }

        $this->info('Email: ' . $user->email);
        $this->info('Name: ' . $user->name);

        return 0;
    }
}