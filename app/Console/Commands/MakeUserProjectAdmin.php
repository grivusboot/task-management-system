<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserProjectAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-project-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user a project admin';

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
            'admin_type' => 'project',
            'is_admin' => false, // Project admins are not application admins
        ]);
        
        $this->info("User '{$user->name}' ({$email}) is now a Project Admin.");
        
        return 0;
    }
}
