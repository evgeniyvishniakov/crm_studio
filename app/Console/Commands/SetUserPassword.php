<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\User;
use Illuminate\Support\Facades\Hash;

class SetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set password for user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password for user {$email} has been set successfully!");
        $this->info("You can now login with email: {$email} and password: {$password}");

        return 0;
    }
}
