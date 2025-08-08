<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list-admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all admin users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $applicationAdmins = User::where('admin_type', 'application')->orWhere('is_admin', true)->get();
        $projectAdmins = User::where('admin_type', 'project')->get();
        
        if ($applicationAdmins->count() > 0) {
            $this->info('Application Admins:');
            $this->table(
                ['Name', 'Email', 'Admin Type'],
                $applicationAdmins->map(function ($user) {
                    return [
                        $user->name,
                        $user->email,
                        $user->admin_type === 'application' ? 'Application Admin' : 'Legacy Admin'
                    ];
                })->toArray()
            );
        } else {
            $this->info('No Application Admins found.');
        }
        
        if ($projectAdmins->count() > 0) {
            $this->info('Project Admins:');
            $this->table(
                ['Name', 'Email', 'Admin Type'],
                $projectAdmins->map(function ($user) {
                    return [
                        $user->name,
                        $user->email,
                        'Project Admin'
                    ];
                })->toArray()
            );
        } else {
            $this->info('No Project Admins found.');
        }
        
        return 0;
    }
}
