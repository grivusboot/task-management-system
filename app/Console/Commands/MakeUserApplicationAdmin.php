<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserApplicationAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-application-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user an application admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }
        
        $user->update([
            'admin_type' => 'application',
            'is_admin' => true, // For backward compatibility
        ]);
        
        $this->info("User '{$user->name}' ({$email}) is now an Application Admin.");
        
        return 0;
    }
}
