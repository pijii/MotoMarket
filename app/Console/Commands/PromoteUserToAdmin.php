<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserToAdmin extends Command
{
    protected $signature = 'users:promote-admin {email : Email address of the existing account} {--force : Skip the confirmation prompt}';

    protected $description = 'Promote an existing MotoMarket account to administrator';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No account exists with that email address.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm("Promote {$user->email} to administrator?")) {
            $this->info('No changes made.');

            return self::SUCCESS;
        }

        $user->forceFill(['role' => 'admin', 'is_active' => true])->save();
        $this->info("{$user->email} is now an active administrator.");

        return self::SUCCESS;
    }
}