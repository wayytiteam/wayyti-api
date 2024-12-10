<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('email');
        $password = $this->secret('password');
        $new_admin = User::create([
            'email' => $email,
            'password' => $password,
            'is_admin' => true
        ]);
        if($new_admin) {
            echo('Admin created successfully');
        } else {
            echo('Something went wrong');
        }
    }
}
